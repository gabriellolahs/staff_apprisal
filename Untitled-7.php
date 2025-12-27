<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

// Load staff info
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

// Load biodata
$biodata = [];
$stmt = $conn->prepare("SELECT * FROM save_biodata WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $biodata = $result->fetch_assoc();
}

$is_locked = $biodata['is_locked'] ?? 0;

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $required = ['title','surname','firstname','office','department','designation','marital_status','dob','pob','nationality','state','senatorial','lga','town','ward','religion','perm_address','contact_address','res_address','phone','gsm','email','blood_group','genotype','nok_name','nok_address','nok_relation','nok_phone','first_place_before','first_date_before','first_place_fpi','appt_type','post_appt','present_appt','regularization','gl','step','confirmation','first_appt_pub','qualifications','union_name','specialization','pfa','pin_code'];
    
    $errors = [];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucwords(str_replace('_', ' ', $field)) . " is required!";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE save_biodata SET 
            title=?, surname=?, firstname=?, lastname=?, office=?, department=?, designation=?, category=?, 
            marital_status=?, sex=?, dob=?, pob=?, nationality=?, state=?, senatorial=?, lga=?, town=?, ward=?, religion=?, 
            perm_address=?, contact_address=?, res_address=?, phone=?, gsm=?, email=?, blood_group=?, genotype=?, 
            spouse_name=?, children=?, nok_name=?, nok_relation=?, nok_phone=?, nok_address=?,
            first_place_before=?, first_date_before=?, first_place_fpi=?, appt_type=?, post_appt=?, present_appt=?, regularization=?,
            gl=?, step=?, confirmation=?, first_appt_pub=?, accommodation=?, qualifications=?, union_name=?, specialization=?, pfa=?, pin_code=?,
            is_locked=1 WHERE staff_id=?");

        $stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssssssssss", 
            $_POST['title'], $staff['surname'], $staff['firstname'], $staff['lastname'] ?? '',
            $_POST['office'], $_POST['department'], $_POST['designation'], $staff['category'],
            $_POST['marital_status'], $staff['gender'], $_POST['dob'], $_POST['pob'], $_POST['nationality'], $_POST['state'], 
            $_POST['senatorial'], $_POST['lga'], $_POST['town'], $_POST['ward'], $_POST['religion'],
            $_POST['perm_address'], $_POST['contact_address'], $_POST['res_address'], $_POST['phone'], $_POST['gsm'], $_POST['email'],
            $_POST['blood_group'], $_POST['genotype'], $_POST['spouse_name'] ?? '', $_POST['children'] ?? 0,
            $_POST['nok_name'], $_POST['nok_relation'], $_POST['nok_phone'], $_POST['nok_address'],
            $_POST['first_place_before'], $_POST['first_date_before'], $_POST['first_place_fpi'], $_POST['appt_type'], $_POST['post_appt'],
            $_POST['present_appt'], $_POST['regularization'], $_POST['gl'], $_POST['step'], $_POST['confirmation'], $_POST['first_appt_pub'],
            $_POST['accommodation'] ?? '', $_POST['qualifications'], $_POST['union_name'], $_POST['specialization'], $_POST['pfa'], $_POST['pin_code'],
            $staff_id
        );

        if ($stmt->execute()) {
            echo "<script>alert('Biodata submitted and LOCKED successfully!'); location.reload();</script>";
        }
    } else {
        echo "<script>alert('Please fill all required fields!');</script>";
    }
}
$passport = "uploads/default.png";
if (!empty($staff['passport']) && file_exists($staff['passport'])) {
    $passport = $staff['passport'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Academic Staff Biodata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #001f3f, #003087); font-family: 'Times New Roman', serif; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); overflow: hidden; }
        .header { background: #003087; color: white; padding: 40px; text-align: center; }
        .header h1 { font-size: 2.5rem; margin: 0; }
        .passport img {
           width: 150px; height: 170px; border: 3px dashed #003087; margin: 20px auto; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; color: #666; }
        .form-section { background: #f8f9fa; padding: 25px; border-radius: 15px; margin: 20px 0; }
        .section-title { background: #003087; color: white; padding: 15px; border-radius: 10px; text-align: center; font-size: 1.4rem; font-weight: bold; margin-bottom: 20px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; }
        .btn-submit { background: #28a745; color: white; padding: 18px 80px; font-size: 1.5rem; border-radius: 50px; font-weight: bold; }
        .locked-alert { background: #d4edda; padding: 40px; border-radius: 20px; text-align: center; font-size: 1.4rem; }
        .required { color: red; }
        
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>THE FEDERAL POLYTECHNIC, ILE-OLUJI</h1>
        <h3>(Office of the Registrar)</h3>
        <h2>STAFF BIODATA FORM</h2>
        <p>Please fill in your information correctly</p>
    </div>

    <div class="p-4">
        <?php if ($is_locked): ?>
            <div class="locked-alert">
                <h3>LOCKED & SUBMITTED</h3>
                <p>Your biodata has been submitted and locked permanently.</p>
                <p>Contact Super Admin to unlock for editing.</p>
                <a href="generate_pdf.php" class="btn btn-danger btn-lg">Download PDF</a>
            </div>
        <?php else: ?>
            <form method="post">
                <div class="text-center mb-4">
                    <div class="passport">
            <img src="<?= htmlspecialchars($passport) ?>" class="profile-photo" alt="Passport">
        </div>
                </div>

                <!-- Staff Information -->
                <div class="form-section">
                    <div class="section-title">STAFF INFORMATION</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Staff ID Number <span class="required">*</span></label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff_id) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Office <span class="required">*</span></label>
                            <select name="office" id="office" class="form-select" required onchange="updateDepartment()">
                                <option value="">-- Select Office --</option>
                                <option value="REGISTRY">REGISTRY</option>
                                <option value="RECTORY">RECTORY</option>
                                <option value="BURSARY">BURSARY</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Department <span class="required">*</span></label>
                            <select name="department" id="department" class="form-select" required>
                                <option value="">-- Select Department --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Designation / Present Post <span class="required">*</span></label>
                            <input type="text" name="designation" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Category</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff['category']) ?>" readonly>
                        </div>
                            <div class="col-md-6">
                            <label>Sub Category</label>
                            <input type="text" class="form-control" value="<?= strtoupper($staff['sub_category']) ?>"readonly>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="form-section">
                    <div class="section-title">PERSONAL INFORMATION</div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label>Title <span class="required">*</span></label>
                            <select name="title" class="form-select" required>
                                <option value="">--</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Miss">Miss</option>
                                <option value="Dr">Dr</option>
                                <option value="Prof">Prof</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>SURNAME</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff['surname']) ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff['firstname']) ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff['lastname'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>Marital Status <span class="required">*</span></label>
                            <select name="marital_status" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Sex</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff['gender']) ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>Date of Birth <span class="required">*</span></label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Place of Birth <span class="required">*</span></label>
                            <input type="text" name="pob" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Nationality <span class="required">*</span></label>
                            <input type="text" name="nationality" class="form-control" value="Nigeria" required>
                        </div>
                        <div class="col-md-3">
                            <label>State of Origin <span class="required">*</span></label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Senatorial District <span class="required">*</span></label>
                            <input type="text" name="senatorial" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Local Govt. of Origin <span class="required">*</span></label>
                            <input type="text" name="lga" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Town of Origin <span class="required">*</span></label>
                            <input type="text" name="town" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Ward <span class="required">*</span></label>
                            <input type="text" name="ward" class="form-control" required>
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
                            <input type="text" name="hobbies" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Address & Contact -->
                <div class="form-section">
                    <div class="section-title">ADDRESS & CONTACT</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label>Permanent Address (not P.O Box) <span class="required">*</span></label>
                            <textarea name="perm_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <label>Contact Address <span class="required">*</span></label>
                            <textarea name="contact_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <label>Residential Address <span class="required">*</span></label>
                            <textarea name="res_address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>GSM <span class="required">*</span></label>
                            <input type="text" name="gsm" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Blood Group <span class="required">*</span></label>
                            <select name="blood_group" class="form-select" required>
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
                            <select name="genotype" class="form-select" required>
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
                            <input type="text" name="spouse_name" class="form-control" require>
                        </div>
                        <div class="col-md-6">
                            <label>Number of Children <span class="required">*</span> </label>
                            <input type="number" name="children" class="form-control" min="0" require>
                        </div>
                    </div>
                </div>

                <!-- Next of Kin -->
                <div class="form-section">
                    <div class="section-title">NEXT OF KIN</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label>Name <span class="required">*</span></label>
                            <input type="text" name="nok_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Relationship <span class="required">*</span></label>
                            <input type="text" name="nok_relation" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Phone <span class="required">*</span></label>
                            <input type="text" name="nok_phone" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label>Address <span class="required">*</span></label>
                            <textarea name="nok_address" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Official Date -->
                <div class="form-section">
                    <div class="section-title">OFFICIAL DATE</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Place of First Appt before FPI <span class="required">*</span></label>
                            <input type="text" name="first_place_before" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of First Appt before FPI <span class="required">*</span></label>
                            <input type="date" name="first_date_before" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Place of First Appt in FPI <span class="required">*</span></label>
                            <input type="text" name="first_place_fpi" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Type of Appt <span class="required">*</span></label>
                            <select name="appt_type" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="Probationary">Probationary</option>
                                <option value="permanent">Permanent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Post On Appt (in FPI) <span class="required">*</span></label>
                            <input type="text" name="post_appt" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of Present Appt <span class="required">*</span></label>
                            <input type="date" name="present_appt" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Date of Regularization <span class="required">*</span></label>
                            <input type="date" name="regularization" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>GL <span class="required">*</span></label>
                            <input type="text" name="gl" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Step <span class="required">*</span></label>
                            <input type="text" name="step" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Date of Confirmation <span class="required">*</span></label>
                            <input type="date" name="confirmation" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Date of First Appt (Public Service) <span class="required">*</span></label>
                            <input type="date" name="first_appt_pub" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Accommodation <span class="required">*</span></label>
                            <input type="text" name="accommodation" class="form-control" require>
                        </div>
                    </div>
                </div>

                <!-- Additional -->
                <div class="form-section">
                    <div class="section-title">ADDITIONAL INFORMATION</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Qualifications <span class="required">*</span></label>
                            <textarea name="qualifications" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label>Union <span class="required">*</span></label>
                            <select name="union_name" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="ASUP">ASUP</option>
                                <option value="SSANIP">SSANIP</option>
                                <option value="NASU">NASU</option>
                                <option value="NONE">NONE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Pension Fund Administrator (PFA) <span class="required">*</span></label>
                            <input type="text" name="pfa" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>PFA Pin Code <span class="required">*</span></label>
                            <input type="text" name="pin_code" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-submit">
                        SUBMIT & LOCK BIODATA (FINAL)
                    </button>
                    <div class="text-danger mt-3 fw-bold fs-5">
                        This action is PERMANENT. Only Super Admin can unlock.
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function updateDepartment() {
    const office = document.getElementById('office').value;
    const dept = document.getElementById('department');
    dept.innerHTML = '<option value="">-- Select Department --</option>';

    const depts = {
        'REGISTRY': ['DIVISION OF PERSONNEL AFFAIRS', 'DIVISION OF COUNCIL AFFAIRS', 'DIVISION OF ACADEMIC AFFAIRS', "REGISTRAR'S OFFICE"],
        'RECTORY': ['RECTORS OFFICE', 'PROCUREMENT UNIT', 'LEGAL UNIT', 'INFORMATION, PROTOCOL AND PASSAGE UNIT', 'SERVICOM', 'ACTU', 'SPORT UNIT', 'HEALTH SERVICES', 'SIWES', 'DIRECTORATE OF PHYSICAL PLANNING, WORK & SERVICES', 'STUDENT AFFAIRS', 'SECURITY UNIT', 'MIS/ICT', 'DIRECTORATE OF CAREER SERVICES'],
        'BURSARY': ['AUDIT UNIT']
    };

    if (depts[office]) {
        depts[office].forEach(d => {
            dept.innerHTML += `<option value="${d}">${d}</option>`;
        });
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>