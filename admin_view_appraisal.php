<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$staff_id = $_GET['staff_id'] ?? '';
if (!$staff_id) {
    die("No staff selected.");
}

// Fetch staff details
$stmt = $conn->prepare("SELECT surname, firstname, lastname, department, faculty FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

if (!$staff) {
    die("Staff not found.");
}

$name = trim($staff['surname'] . ' ' . $staff['firstname'] . ' ' . $staff['lastname']);

// Fetch appraisal data
$result = $conn->query("SELECT * FROM staff_appraisal WHERE staff_id = '$staff_id' ORDER BY id");

$data = [
    'promotion' => [],
    'education' => [],
    'academic' => [],
    'professional' => [],
    'publication' => []
];

$status = ['is_locked' => 0, 'admin_approved' => 0, 'admin_comment' => '', 'submitted_at' => ''];

while ($row = $result->fetch_assoc()) {
    $status['is_locked'] = $row['is_locked'];
    $status['admin_approved'] = $row['admin_approved'];
    $status['admin_comment'] = $row['admin_comment'] ?? '';
    $status['submitted_at'] = $row['submitted_at'];

    switch ($row['entry_type']) {
        case 'promotion':
            $data['promotion'][] = [
                $row['field1'], // date
                $row['field2'], // from position
                $row['field3'], // from GL-Step
                $row['field4'], // to position
                $row['field5']  // to GL-Step
            ];
            break;
        case 'education':
            $data['education'][] = [$row['field1'], $row['field2'], $row['field3']];
            break;
        case 'academic':
            $data['academic'][] = [$row['field1'], $row['field2'], $row['field3'], $row['field4']];
            break;
        case 'professional':
            $data['professional'][] = [$row['field1'], $row['field2'], $row['field3']];
            break;
        case 'publication':
            $data['publication'][] = [$row['field1'], $row['field2'], $row['field3']];
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View - <?= $staff_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #001f3f; padding: 20px; }
        .card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .header { background: #003087; color: white; padding: 30px; border-radius: 20px 20px 0 0; text-align: center; }
        table { font-size: 1rem; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .btn-download { padding: 15px 40px; font-size: 1.3rem; border-radius: 50px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>STAFF APPRAISAL REVIEW</h1>
            <h3><?= strtoupper($name) ?> (<?= $staff_id ?>)</h3>
            <p><?= $staff['department'] ?> • <?= $staff['faculty'] ?></p>
        </div>

        <div class="card-body p-5">

            <!-- STATUS -->
            <div class="text-center mb-5 p-4 rounded <?= $status['admin_approved'] ? 'status-approved' : 'status-pending' ?>">
                <h2>
                    <?php if ($status['admin_approved']): ?>
                        APPROVED
                    <?php elseif ($status['is_locked']): ?>
                        PENDING APPROVAL
                    <?php else: ?>
                        NOT SUBMITTED YET
                    <?php endif; ?>
                </h2>
                <?php if ($status['submitted_at']): ?>
                    <p>Submitted: <strong><?= date('d M Y, h:i A', strtotime($status['submitted_at'])) ?></strong></p>
                <?php endif; ?>
            </div>

            <!-- PROMOTION -->
            <?php if (!empty($data['promotion'])): ?>
                <h4 class="text-primary mb-3"><strong>PROMOTION RECORD</strong></h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>From Position</th>
                            <th>From GL-Step</th>
                            <th>To Position</th>
                            <th>To GL-Step</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['promotion'] as $p): ?>
                            <tr>
                                <td><?= $p[0] ?: '-' ?></td>
                                <td><?= $p[1] ?: '-' ?></td>
                                <td><?= $p[2] ?: '-' ?></td>
                                <td><?= $p[3] ?: '-' ?></td>
                                <td><?= $p[4] ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- EDUCATION -->
            <?php if (!empty($data['education'])): ?>
                <h4 class="text-primary mb-3 mt-5"><strong>EDUCATIONAL QUALIFICATIONS</strong></h4>
                <table class="table table-bordered">
                    <thead class="table-dark"><tr><th>Institution</th><th>Year Admitted</th><th>Year Graduated</th></tr></thead>
                    <tbody>
                        <?php foreach ($data['education'] as $e): ?>
                            <tr><td><?= $e[0] ?></td><td><?= $e[1] ?: '-' ?></td><td><?= $e[2] ?: '-' ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- ACADEMIC -->
            <?php if (!empty($data['academic'])): ?>
                <h4 class="text-primary mb-3 mt-5"><strong>ACADEMIC QUALIFICATIONS</strong></h4>
                <table class="table table-bordered">
                    <thead class="table-dark"><tr><th>Certificate</th><th>Date</th><th>Grade</th><th>Awarding Body</th></tr></thead>
                    <tbody>
                        <?php foreach ($data['academic'] as $a): ?>
                            <tr><td><?= $a[0] ?></td><td><?= $a[1] ?: '-' ?></td><td><?= $a[2] ?: '-' ?></td><td><?= $a[3] ?: '-' ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- PROFESSIONAL -->
            <?php if (!empty($data['professional'])): ?>
                <h4 class="text-primary mb-3 mt-5"><strong>PROFESSIONAL QUALIFICATIONS</strong></h4>
                <table class="table table-bordered">
                    <thead class="table-dark"><tr><th>Certificate</th><th>Date</th><th>Awarding Body</th></tr></thead>
                    <tbody>
                        <?php foreach ($data['professional'] as $pr): ?>
                            <tr><td><?= $pr[0] ?></td><td><?= $pr[1] ?: '-' ?></td><td><?= $pr[2] ?: '-' ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- PUBLICATIONS -->
            <?php if (!empty($data['publication'])): ?>
                <h4 class="text-primary mb-3 mt-5"><strong>PUBLICATIONS</strong></h4>
                <table class="table table-bordered">
                    <thead class="table-dark"><tr><th>Title</th><th>Journal/Conference</th><th>Year</th></tr></thead>
                    <tbody>
                        <?php foreach ($data['publication'] as $pub): ?>
                            <tr><td><?= $pub[0] ?></td><td><?= $pub[1] ?: '-' ?></td><td><?= $pub[2] ?: '-' ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- ADMIN COMMENT -->
            <?php if ($status['admin_comment']): ?>
                <div class="mt-5 p-4 bg-light border rounded">
                    <h5>Admin Comment:</h5>
                    <p><?= nl2br(htmlspecialchars($status['admin_comment'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- DOWNLOAD BUTTON -->
            <div class="text-center mt-5">
                <a href="generate_appraisal_pdf.php?staff_id=<?= $staff_id ?>" class="btn btn-success btn-download">
                    DOWNLOAD OFFICIAL PDF
                </a>
                <br><br>
                <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>