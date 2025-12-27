<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

// Fetch staff + biodata
$staff = $conn->query("SELECT s.*, b.* FROM staff s 
                       LEFT JOIN save_biodata b ON s.staff_id = b.staff_id 
                       WHERE s.staff_id = '$staff_id'")->fetch_assoc();

if (!$staff) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check if biodata is locked
if (empty($staff['is_locked']) || $staff['is_locked'] != 1) {
    echo "<div class='alert alert-danger text-center'>Please complete and lock your BIODATA first before filling appraisal!</div>";
    echo "<a href='biodata.php' class='btn btn-primary'>Go to Biodata</a>";
    exit();
}

// Fetch current appraisal status
$appraisal = $conn->query("SELECT * FROM staff_appraisal WHERE staff_id = '$staff_id'")->fetch_assoc();
$status = $appraisal['status'] ?? 'draft';

$readonly = in_array($status, ['hod_approved','dean_approved','final_approved']) ? 'readonly' : '';
$disabled = $readonly ? 'disabled' : '';

// Passport
$passport = $staff['passport'] && file_exists($staff['passport']) ? $staff['passport'] : 'uploads/default.png';

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'draft') {
    // Collect appraisal fields (add more as needed)
    $fields = [
        'courses_attended', 'teaching_exp', 'courses_taught', 'supervision',
        'research_progress', 'research_completed', 'thesis', 'authored_books',
        'journal_articles', 'conferences', 'poly_activities', 'external_activities'
    ];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = $_POST[$f] ?? '';
    }

    $data['status'] = 'pending_hod'; // Send to HOD
    $data['submitted_at'] = date('Y-m-d H:i:s');

    if ($appraisal) {
        // UPDATE
        $set = [];
        foreach ($data as $k => $v) {
            $set[] = "$k = ?";
        }
        $sql = "UPDATE staff_appraisal SET " . implode(', ', $set) . " WHERE staff_id = ?";
        $params = array_values($data);
        $params[] = $staff_id;
    } else {
        // INSERT
        $columns = implode(', ', array_keys($data));
        $placeholders = str_repeat('?,', count($data) - 1) . '?';
        $sql = "INSERT INTO staff_appraisal (staff_id, $columns) VALUES (?, $placeholders)";
        $params = [$staff_id];
        $params = array_merge($params, array_values($data));
    }

    $types = str_repeat('s', count($params));
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $status = 'pending_hod';
    $message = "Appraisal submitted successfully! Waiting for HOD review.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Appraisal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #001f3f, #003087); padding: 20px; }
        .container { max-width: 1200px; margin: 40px auto; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
        .header { background: #003087; color: white; padding: 40px; text-align: center; border-radius: 15px; margin: -30px -30px 30px; }
        .passport-box { width: 160px; height: 180px; border: 5px solid white; margin: 20px auto; border-radius: 15px; overflow: hidden; }
        .section-title { background: #003087; color: white; padding: 18px; border-radius: 15px; text-align: center; font-size: 1.6rem; margin: 40px 0 20px; }
        .biodata-summary { background: #f8f9fa; padding: 20px; border-radius: 15px; margin-bottom: 30px; }
        .status-badge { font-size: 1.2rem; padding: 10px 20px; border-radius: 50px; }
        .btn-submit { background: #28a745; color: white; padding: 20px 100px; font-size: 1.8rem; border-radius: 50px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>THE FEDERAL POLYTECHNIC, ILE-OLUJI</h1>
        <h3>ANNUAL APPRAISAL REPORT</h3>
        <h4>ACADEMIC STAFF</h4>
        <div class="passport-box">
            <img src="<?= htmlspecialchars($passport) ?>" alt="Passport">
        </div>
    </div>

    <div class="p-4">
        <a href="dashboard.php" class="btn btn-light btn-lg mb-4">← Back to Dashboard</a>

        <!-- BIODATA SUMMARY -->
        <div class="biodata-summary">
            <h4 class="text-primary">BIODATA SUMMARY</h4>
            <div class="row">
                <div class="col-md-4"><strong>Staff ID:</strong> <?= $staff_id ?></div>
                <div class="col-md-4"><strong>Name:</strong> <?= $staff['surname'] ?> <?= $staff['firstname'] ?> <?= $staff['lastname'] ?></div>
                <div class="col-md-4"><strong>School:</strong> <?= $staff['school'] ?></div>
                <div class="col-md-4"><strong>Department:</strong> <?= $staff['department'] ?></div>
                <div class="col-md-4"><strong>Designation:</strong> <?= $staff['designation'] ?></div>
                <div class="col-md-4"><strong>Email:</strong> <?= $staff['email'] ?></div>
            </div>
        </div>

        <!-- CURRENT STATUS -->
        <div class="text-center mb-4">
            <span class="badge status-badge 
                <?= $status=='pending_hod' ? 'bg-warning' : 
                   ($status=='hod_approved' ? 'bg-info' : 
                   ($status=='final_approved' ? 'bg-success' : 'bg-secondary')) ?>">
                Current Status: <?= ucwords(str_replace('_', ' ', $status)) ?>
            </span>
        </div>

        <?php if ($status === 'draft'): ?>
            <form method="post">
                <div class="section-title">Courses Attended</div>
                <textarea name="courses_attended" class="form-control" rows="4" placeholder="List courses, workshops attended..." required><?= $appraisal['courses_attended'] ?? '' ?></textarea>

                <div class="section-title">Teaching Experience</div>
                <textarea name="teaching_exp" class="form-control" rows="5" required><?= $appraisal['teaching_exp'] ?? '' ?></textarea>

                <div class="section-title">Courses Taught This Session</div>
                <textarea name="courses_taught" class="form-control" rows="4" required><?= $appraisal['courses_taught'] ?? '' ?></textarea>

                <div class="section-title">Supervision</div>
                <textarea name="supervision" class="form-control" rows="3" required><?= $appraisal['supervision'] ?? '' ?></textarea>

                <div class="section-title">Research in Progress</div>
                <textarea name="research_progress" class="form-control" rows="4"><?= $appraisal['research_progress'] ?? '' ?></textarea>

                <div class="section-title">Publications</div>
                <textarea name="journal_articles" class="form-control" rows="6" required><?= $appraisal['journal_articles'] ?? '' ?></textarea>

                <div class="section-title">Conferences Attended</div>
                <textarea name="conferences" class="form-control" rows="4"><?= $appraisal['conferences'] ?? '' ?></textarea>

                <div class="section-title">Extra Curricular Activities</div>
                <textarea name="poly_activities" class="form-control" rows="3"><?= $appraisal['poly_activities'] ?? '' ?></textarea>
                <textarea name="external_activities" class="form-control" rows="3" class="mt-3"><?= $appraisal['external_activities'] ?? '' ?></textarea>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-submit">
                        SUBMIT TO HOD FOR REVIEW
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Your appraisal is currently: <strong><?= ucwords(str_replace('_', ' ', $status)) ?></strong><br>
                <?php if ($status === 'pending_hod'): ?>
                    Waiting for HOD review...
                <?php elseif ($status === 'hod_approved'): ?>
                    HOD approved. Waiting for Dean...
                <?php elseif ($status === 'final_approved'): ?>
                    Appraisal fully approved!
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

//"Create a set_up file: for bio_data set up. each junior staff wil able to set 
their bio data informatio and also added staff_apprisal file: each bio_data wil be store in it"  