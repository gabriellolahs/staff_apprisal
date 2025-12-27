<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

/* =========================
   SUPER ADMIN IDS
========================= */
$super_admins = ['FedpolelAdmin', 'FedpolelAdmin123'];

// Fetch staff record
$stmt = $conn->prepare("SELECT * FROM users WHERE staff_id = ?");
$stmt = $conn->prepare("SELECT * FROM users WHERE staff_id = ?");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

// Fetch biodata if exists
$stmt = $conn->prepare("SELECT * FROM staff_biodata WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$biodata = $stmt->get_result()->fetch_assoc();

$is_locked = $biodata['is_locked'] ?? 0;

// Load schools
$schools = $conn->query("SELECT * FROM schools ORDER BY school_name")->fetch_all(MYSQLI_ASSOC);


if (!isset($_SESSION['staff_id'])) {
    die("Unauthorized access");
}

$staff_id = $_SESSION['staff_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

// Convert complex sections to JSON
$extra_data = json_encode([
    'promotion'   => $_POST['promo_date'] ?? [],
    'education'   => $_POST['edu_school'] ?? [],
    'academic'    => $_POST['acad_cert'] ?? [],
    'professional'=> $_POST['prof_cert'] ?? [],
    'publications'=> $_POST['title'] ?? []
]);

// Check if biodata exists
$stmt = $conn->prepare("SELECT id, is_locked FROM staff_biodata WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {

    $row = $res->fetch_assoc();

    if ($row['is_locked'] == 1) {
        $_SESSION['error_msg'] = "Biodata is locked. Contact Super Admin.";
        header("Location: biodata.php");
        exit;
    }

    // UPDATE
    $sql = "UPDATE staff_biodata SET
        school_id = ?, department_id = ?, designation = ?, title = ?, marital_status = ?, dob = ?, pob = ?,
        perm_address = ?, contact_address = ?, res_address = ?, gsm = ?, email = ?,
        qualifications = ?, specialization = ?, spouse_name = ?, children = ?,
        nok_name = ?, nok_relation = ?, nok_phone = ?, nok_address = ?,
        first_place_before = ?, first_date_before = ?, first_place_fpi = ?, appt_type = ?,
        post_appt = ?, present_appt = ?, regularization = ?, gl = ?, step = ?,
        confirmation = ?, first_appt_pub = ?, accommodation = ?, union_name = ?, pfa = ?, pin_code = ?,
        hobbies = ?, extra_data = ?, is_locked = 1
        WHERE staff_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iissssssssssssssssssssssssssssssssssss",
        $_POST['school'], $_POST['department'], $_POST['designation'], $_POST['title'], $_POST['marital_status'],
        $_POST['dob'], $_POST['pob'], $_POST['perm_address'], $_POST['contact_address'], $_POST['res_address'],
        $_POST['gsm'], $_POST['email'], $_POST['qualifications'], $_POST['specialization'],
        $_POST['spouse_name'], $_POST['children'], $_POST['nok_name'], $_POST['nok_relation'],
        $_POST['nok_phone'], $_POST['nok_address'], $_POST['first_place_before'],
        $_POST['first_date_before'], $_POST['first_place_fpi'], $_POST['appt_type'],
        $_POST['post_appt'], $_POST['present_appt'], $_POST['regularization'], $_POST['gl'], $_POST['step'],
        $_POST['confirmation'], $_POST['first_appt_pub'], $_POST['accommodation'],
        $_POST['union_name'], $_POST['pfa'], $_POST['pin_code'], $_POST['hobbies'], $extra_data, $staff_id
    );

} else {

    // INSERT
    $sql = "INSERT INTO staff_biodata (
        staff_id, school_id, department_id, designation, title, marital_status, dob, pob,
        perm_address, contact_address, res_address, gsm, email, qualifications,
        spouse_name, children, nok_name, nok_relation, nok_phone, nok_address,
        first_place_before, first_date_before, first_place_fpi, appt_type,
        post_appt, present_appt, regularization, gl, step, confirmation,
        first_appt_pub, accommodation, union_name, pfa, pin_code, hobbies,
        extra_data, is_locked
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "siisssssssssssssssssssssssssssssssssss",
        $staff_id, $_POST['school'], $_POST['department'], $_POST['designation'], $_POST['title'],
        $_POST['marital_status'], $_POST['dob'], $_POST['pob'], $_POST['perm_address'],
        $_POST['contact_address'], $_POST['res_address'], $_POST['gsm'], $_POST['email'],
        $_POST['qualifications'], $_POST['spouse_name'], $_POST['children'],
        $_POST['nok_name'], $_POST['nok_relation'], $_POST['nok_phone'], $_POST['nok_address'],
        $_POST['first_place_before'], $_POST['first_date_before'], $_POST['first_place_fpi'],
        $_POST['appt_type'], $_POST['post_appt'], $_POST['present_appt'],
        $_POST['regularization'], $_POST['gl'], $_POST['step'], $_POST['confirmation'],
        $_POST['first_appt_pub'], $_POST['accommodation'], $_POST['union_name'],
        $_POST['pfa'], $_POST['pin_code'], $_POST['hobbies'], $extra_data
    );
}

$stmt->execute();

$_SESSION['success_msg'] = "Biodata submitted and locked successfully.";
header("Location: biodata.php");
exit;

$readonly = $is_locked ? 'readonly' : '';
$disabled = $is_locked ? 'disabled' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Biodata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #001f3f, #003087); padding: 20px; }
        .container { max-width: 1200px; margin: 40px auto; background: white; border-radius: 20px; padding: 40px; box-shadow: 0 25px 80px rgba(0,0,0,0.6); }
        .header { background: #003087; color: white; padding: 60px 40px; text-align: center; border-radius: 20px 20px 0 0; margin: -40px -40px 40px; }
        .header h1 { font-size: 3rem; margin: 0; font-weight: bold; }
        .passport-box { width: 170px; height: 190px; border: 6px solid white; margin: 30px auto; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.5); }
        .passport-box img { width: 100%; height: 100%; object-fit: cover; }
        .section-title { background: #003087; color: white; padding: 20px; border-radius: 15px; text-align: center; font-size: 1.7rem; font-weight: bold; margin: 50px 0 25px; }
        .btn-submit { background: #28a745; color: white; padding: 25px 140px; font-size: 2rem; border-radius: 70px; font-weight: bold; }
        .locked-state { background: #d4edda; color: #155724; padding: 70px; border-radius: 25px; text-align: center; border: 4px solid #28a745; margin: 40px 0; }
        .super-unlock { background: #fd7e14; color: white; padding: 15px 40px; font-size: 1.5rem; border-radius: 50px; }
        .alert { border-radius: 15px; padding: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>THE FEDERAL POLYTECHNIC, ILE-OLUJI</h1>
        <h3>STAFF BIODATA FORM</h3>
        <div class="passport-box">
            <img src="<?= htmlspecialchars($passport) ?>" alt="Passport Photo">
        </div>
    </div>

    <div class="p-5">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                <h4><?= $success_msg ?></h4>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger">
                <h4><?= $error_msg ?></h4>
            </div>
        <?php endif; ?>

        <?php if ($is_locked): ?>
            <div class="locked-state">
                <h2>BIODATA SUBMITTED & LOCKED</h2>
                <p class="lead">Your biodata is now permanent and view-only.</p>
                <?php if (in_array($staff_id, $super_admins)): ?>
                    <form method="post" class="mt-4">
                        <button type="submit" name="unlock" class="btn super-unlock">
                            <i class="fas fa-unlock"></i> SUPER ADMIN: UNLOCK FOR EDIT
                        </button>
                    </form>
                <?php endif; ?>
                <a href="appraisal.php" class="btn btn-primary btn-lg px-5 mt-4">
                    <i class="fas fa-file-alt"></i> Proceed to Appraisal Form
                </a>
            </div>
        <?php else: ?>
            <form method="post" enctype="multipart/form-data">
                <!-- STAFF INFORMATION -->
                <div class="section-title">STAFF INFORMATION</div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label>Staff ID</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff_id) ?>" <?= $is_locked ? 'readonly' : '' ?> readonly>
                    </div>
                    <div class="col-md-4">
                        <label>School</label>
                        <select name="school" id="school" class="form-select" onchange="updateDepartment()" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">-- Select School --</option>
                            <?php foreach ($schools as $s): ?>
                                <option value="<?= htmlspecialchars($s['school_name']) ?>" <?= ($biodata['school'] ?? '') === $s['school_name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['school_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Department</label>
                        <select name="department" id="department" class="form-select" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">-- Select Department --</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Designation / Present Post</label>
                        <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($biodata['designation'] ?? '') ?> " <?= $is_locked ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-6">
                        <label>Category</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff['category']) ?>" <?= $is_locked ? 'readonly' : '' ?> readonly>
                    </div>
                </div>

                <!-- PERSONAL INFORMATION -->
                            <!--STaff bio data_information -->
                <div class="section-title">PERSONAL INFORMATION</div>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label>Title</label>
                        <select name="title" class="form-select" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">-- Select --</option>
                            <option value="Mr" <?= ($biodata['title'] ?? '') == 'Mr' ? 'selected' : '' ?>>Mr</option>
                            <option value="Mrs" <?= ($biodata['title'] ?? '') == 'Mrs' ? 'selected' : '' ?>>Mrs</option>
                            <option value="Miss" <?= ($biodata['title'] ?? '') == 'Miss' ? 'selected' : '' ?>>Miss</option>
                            <option value="Dr" <?= ($biodata['title'] ?? '') == 'Dr' ? 'selected' : '' ?>>Dr</option>
                            <option value="Prof" <?= ($biodata['title'] ?? '') == 'Prof' ? 'selected' : '' ?>>Prof</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Surname</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff['surname']) ?>" <?= $is_locked ? 'readonly' : '' ?> readonly>
                    </div>
                    <div class="col-md-3">
                        <label>First Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff['firstname']) ?>"<?= $is_locked ? 'readonly' : '' ?> readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Middle Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff['lastname'] ?? '') ?>" <?= $is_locked ? 'readonly' : '' ?> readonly>
                    </div>                    <div class="col-md-3">
                    <label>Marital Status <span class="required">*</span></label>
                        <select name="marital_status" class="form-select" <?= $is_locked ? 'disabled' : '' ?> required>
                            <option value="">-- Select --</option>
                            <option value="Single" <?= ($biodata['marital_status'] ?? '') == 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($biodata['marital_status'] ?? '') == 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Divorced" <?= ($biodata['marital_status'] ?? '') == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                            <option value="Widowed" <?= ($biodata['marital_status'] ?? '') == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Sex</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($staff['gender']) ?>" <?= $is_locked ? 'readonly' : '' ?> readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Date of Birth <span class="required">*</span></label>
                        <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($biodata['dob'] ?? '') ?>" <?= $is_locked ? 'readonly' : '' ?> required>
                    </div>
                    <div class="col-md-3">
                        <label>Place of Birth <span class="required">*</span></label>
                        <input type="text" name="pob" class="form-control" value="<?= htmlspecialchars($biodata['pob'] ?? '') ?>" <?= $is_locked ? 'readonly' : '' ?> required>
                    </div>
                    <div class="col-md-3">
                            <label>Nationality <span class="required">*</span></label>
                            <input type="text" name="nationality" class="form-control" value="Nigeria" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>State of Origin <span class="required">*</span></label>
                            <input type="text" name="state" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Senatorial District <span class="required">*</span></label>
                            <input type="text" name="senatorial" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Local Govt. of Origin <span class="required">*</span></label>
                            <input type="text" name="lga" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Town of Origin <span class="required">*</span></label>
                            <input type="text" name="town" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Ward <span class="required">*</span></label>
                            <input type="text" name="ward" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Religion <span class="required">*</span></label>
                            <select name="religion" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="Christianity">Christianity</option>
                                <option value="Islam">Islam</option>
                                <option value="Traditional">Traditional</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Hobbies <span class="required">*</span></label>
                            <input type="text" name="hobbies" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                    </div>
                </div>

                <!-- Address & Contact -->
                <div class="form-section">
                    <div class="section-title">CONTACT ADDRESS</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label>Permanent Address (not P.O Box) <span class="required">*</span></label>
                            <textarea name="perm_address" class="form-control" rows="2" <?= $is_locked ? 'readonly' : '' ?> required></textarea>
                        </div>
                        <div class="col-12">
                            <label>Contact Address <span class="required">*</span></label>
                            <textarea name="contact_address" class="form-control" rows="2" <?= $is_locked ? 'readonly' : '' ?> required></textarea>
                        </div>
                        <div class="col-12">
                            <label>Residential Address <span class="required">*</span></label>
                            <textarea name="res_address" class="form-control" rows="2" <?= $is_locked ? 'readonly' : '' ?> required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>GSM <span class="required">*</span></label>
                            <input type="text" name="gsm" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-4">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-4">
                            <label>Blood Group <span class="required">*</span></label>
                            <select name="blood_group" class="form-select" <?= $is_locked ? 'disabled' : '' ?> required>
                                <option value="">-- Select --</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Genotype <span class="required">*</span></label>
                            <select name="genotype" class="form-select" <?= $is_locked ? 'disabled' : '' ?> required>
                                <option value="">-- Select --</option>
                                <option value="AA">AA</option>
                                <option value="AS">AS</option>
                                <option value="SS">SS</option>
                                <option value="AC">AC</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Family -->
                <div class="form-section">
                    <div class="section-title">FAMILY INFORMATION</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Spouse Name <span class="required">*</span></label>
                            <input type="text" name="spouse_name" class="form-control" <?= $is_locked ? 'readonly' : '' ?> require>
                        </div>
                        <div class="col-md-6">
                            <label>Number of Children <span class="required">*</span> </label>
                            <input type="number" name="children" class="form-control" min="0" <?= $is_locked ? 'readonly' : '' ?> require>
                        </div>
                    </div>
                </div>

                <!-- Next of Kin -->
                <div class="form-section">
                    <div class="section-title">NEXT OF KIN</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Name <span class="required">*</span></label>
                            <input type="text" name="nok_name" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-4">
                            <label>Relationship <span class="required">*</span></label>
                            <input type="text" name="nok_relation" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-4">
                            <label>Phone <span class="required">*</span></label>
                            <input type="text" name="nok_phone" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-12">
                            <label>Address <span class="required">*</span></label>
                            <textarea name="nok_address" class="form-control" rows="2" <?= $is_locked ? 'readonly' : '' ?> required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Official Date -->
                <div class="form-section">
                    <div class="section-title">OFFICIAL</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Place of First Appt before FPI <span class="required">*</span></label>
                            <input type="text" name="first_place_before" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of First Appt before FPI <span class="required">*</span></label>
                            <input type="date" name="first_date_before" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>Place of First Appt in FPI <span class="required">*</span></label>
                            <input type="text" name="first_place_fpi" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>Type of Appt <span class="required">*</span></label>
                            <select name="appt_type" class="form-select" <?= $is_locked ? 'disabled' : '' ?>required>
                                <option value="">-- Select --</option>
                                <option value="Probationary">Probationary</option>
                                <option value="permanent">Permanent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Post On Appt (in FPI) <span class="required">*</span></label>
                            <input type="text" name="post_appt" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of Present Appt <span class="required">*</span></label>
                            <input type="date" name="present_appt" class="form-control"<?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of Regularization <span class="required">*</span></label>
                            <input type="date" name="regularization" class="form-control"<?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>GL <span class="required">*</span></label>
                            <input type="text" name="gl" class="form-control"<?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Step <span class="required">*</span></label>
                            <input type="text" name="step" class="form-control"<?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-3">
                            <label>Date of Confirmation <span class="required">*</span></label>
                            <input type="date" name="confirmation" class="form-control" <?= $is_locked ? 'readonly' : '' ?>required>
                        </div>
                        <div class="col-md-3">
                            <label>Date of First Appt (Public Service) <span class="required">*</span></label>
                            <input type="date" name="first_appt_pub" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>Accommodation <span class="required">*</span></label>
                            <input type="text" name="accommodation" class="form-control"<?= $is_locked ? 'readonly' : '' ?> require>
                        </div>
                    </div>
                </div>

                <!-- Additional -->
                <div class="form-section">
                    <div class="section-title">ADDITIONAL INFORMATION</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Qualifications <span class="required">*</span></label>
                            <textarea name="qualifications" class="form-control" rows="3" <?= $is_locked ? 'readonly' : '' ?>required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label>Union <span class="required">*</span></label>
                            <select name="union_name" class="form-select" <?= $is_locked ? 'disabled' : '' ?> required>
                                <option value="">-- Select --</option>
                                <option value="ASUP">ASUP</option>
                                <option value="SSANIP">SSANIP</option>
                                <option value="NASU">NASU</option>
                                <option value="NONE">NONE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Pension Fund Administrator (PFA) <span class="required">*</span></label>
                            <input type="text" name="pfa" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6">
                            <label>PFA Pin Code <span class="required">*</span></label>
                            <input type="text" name="pin_code" class="form-control" <?= $is_locked ? 'readonly' : '' ?> required>
                        </div>
                    </div>
                </div>

               <!-- PROMOTION RECORD -->
                <div class="section-title">PROMOTION / ADVANCEMENT RECORD</div>
                <div id="promotionRows">
                    <div class="row g-3 mb-3 align-items-center border-bottom pb-3">
                        <div class="col-md-1"><input type="date" class="form-control" name="promo_date[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><input type="text" class="form-control" placeholder="From Position" name="from_position[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="GL" name="from_gl[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="Step" name="from_step[]"<?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><input type="text" class="form-control" placeholder="To Position" name="to_position[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="GL" name="to_gl[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="Step" name="to_step[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><button type="button" class="btn btn-success btn-sm w-100" onclick="addRow('promotion')">+ Add Row</button></div>
                    </div>
                </div>

                <!-- EDUCATION -->
                <div class="section-title">EDUCATIONAL INSTITUTIONS ATTENDED</div>
                <div id="educationRows">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><input type="text" class="form-control" placeholder="School Name" name="edu_school[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><input type="number" class="form-control" placeholder="Year Admitted" name="edu_admit[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><input type="number" class="form-control" placeholder="Year Graduated" name="edu_grad[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><button type="button" class="btn btn-success btn-sm w-100" onclick="addRow('education')">+ Add</button></div>
                    </div>
                </div>

                <!-- ACADEMIC QUALIFICATIONS -->
                <div class="section-title">ACADEMIC QUALIFICATIONS</div>
                <div id="academicRows">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><input type="text" class="form-control" placeholder="Certificate/Degree" name="acad_cert[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><input type="date" class="form-control" name="acad_date[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-2"><input type="text" class="form-control" placeholder="Grade" name="acad_grade[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-3"><input type="text" class="form-control" placeholder="Awarding Body" name="acad_body[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-1"><button type="button" class="btn btn-success btn-sm" onclick="addRow('academic')">+</button></div>
                    </div>
                </div>

                <!-- PROFESSIONAL QUALIFICATIONS -->
                <div class="section-title">PROFESSIONAL QUALIFICATIONS</div>
                <div id="profRows">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5"><input type="text" class="form-control" placeholder="Certificate" name="prof_cert[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-3"><input type="date" class="form-control" name="prof_date[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-3"><input type="text" class="form-control" placeholder="Awarding Body" name="prof_body[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                        <div class="col-md-1"><button type="button" class="btn btn-success btn-sm" onclick="addRow('prof')" >+</button></div>
                    </div>
                </div>

                <!-- PUBLICATIONS -->
                <div class="section-title">PUBLICATIONS, JOURNALS & CONFERENCE PAPERS</div>
                <div id="publicationContainer">
                    <div class="border p-4 rounded mb-3 bg-light">
                        <div class="row g-3">
                            <div class="col-md-5"><input type="text" class="form-control" placeholder="Title of Article/Paper" name="title[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                            <div class="col-md-3"><input type="text" class="form-control" placeholder="Journal / Conference Name" name="journal[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                            <div class="col-md-2"><input type="number" class="form-control" placeholder="Year" name="pub_year[]" <?= $is_locked ? 'readonly' : '' ?>></div>
                            <div class="col-md-2"><input type="file" class="form-control" name="publication_files[]" accept=".pdf,.doc,.docx" <?= $is_locked ? 'readonly' : '' ?>></div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary mb-4" onclick="addPublication()">+ Add Another Publication</button>

                <div class="text-center mt-5 p-5 bg-danger bg-opacity-10 rounded border border-danger">
                    <h2 class="text-danger">FINAL SUBMISSION</h2>
                    <p class="lead fw-bold">This action is IRREVERSIBLE. Your biodata will be LOCKED permanently.</p>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-lock"></i> SUBMIT & LOCK BIODATA
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
                            <!--bio_data information end here-->

<script>
 // ADD ROW (Promotion, Education, etc.)
function addRow(type) {
    const container = document.getElementById(type + 'Rows');
    const template = container.children[0]; // First row as template
    const newRow = template.cloneNode(true);

    // Clear all inputs in the new row
    newRow.querySelectorAll('input, select').forEach(input => {
        input.value = '';
        input.name = input.name.replace(/\[\d+\]/, '[' + (container.children.length) + ']'); // Optional: fix name index
    });

    // Add DELETE button (except first row)
    if (container.children.length > 0) {
        const deleteBtn = document.createElement('div');
        deleteBtn.className = 'col-md-1 d-flex align-items-center';
        deleteBtn.innerHTML = `
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.row').remove()">
                <i class="fas fa-trash"></i> Delete
            </button>
        `;
        newRow.appendChild(deleteBtn);
    }

    container.appendChild(newRow);
}

// ADD PUBLICATION ROW
function addPublication() {
    const container = document.getElementById('publicationContainer');
    const template = container.children[0];
    const newRow = template.cloneNode(true);

    // Clear inputs
    newRow.querySelectorAll('input').forEach(input => input.value = '');

    // Add DELETE button
    const deleteBtn = document.createElement('div');
    deleteBtn.className = 'col-md-2 mt-3';
    deleteBtn.innerHTML = `
        <button type="button" class="btn btn-danger w-100" onclick="this.closest('.border').remove()">
            <i class="fas fa-trash"></i> Remove
        </button>
    `;
    newRow.querySelector('.row').appendChild(deleteBtn);

    container.appendChild(newRow);
}

// DELETE ROW (for first row — optional protection)
function removeRow(btn) {
    if (document.querySelectorAll('#promotionRows .row').length <= 1) {
        alert("Cannot delete the last row!");
        return;
    }
    btn.closest('.row').remove();
}
// DYNAMIC DEPARTMENT LOADER — WORKS 100% WITH YOUR DATABASE
function updateDepartment() {
    const schoolName = document.getElementById('school').value;
    const deptSelect = document.getElementById('department');
    
    deptSelect.innerHTML = '<option value="">-- Select Department --</option>';

    <?php
    // Generate JavaScript object from PHP
    $js_depts = [];
    foreach ($schools as $school) {
        $depts = $conn->query("SELECT department_name FROM departments WHERE school_id = {$school['id']}")->fetch_all(MYSQLI_ASSOC);
        $dept_names = array_column($depts, 'department_name');
        $js_depts[$school['school_name']] = $dept_names;
    }
    echo "const departments = " . json_encode($js_depts) . ";";
    ?>

    if (departments[schoolName]) {
        departments[schoolName].forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            if ('<?= addslashes($biodata['department'] ?? '') ?>' === dept) {
                option.selected = true;
            }
            deptSelect.appendChild(option);
        });
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', updateDepartment);
</script>
</body>
</html>