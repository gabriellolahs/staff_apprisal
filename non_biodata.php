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

// Load biodata + extra data
$biodata = [];
$extra = ['promotion' => [], 'education' => [], 'academic' => [], 'professional' => [], 'publications' => []];
$stmt = $conn->prepare("SELECT * FROM save_biodata WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $biodata = $result->fetch_assoc();
    $extra = json_decode($biodata['extra_data'] ?? '{}', true) ?: $extra;
}

$is_locked = $biodata['is_locked'] ?? 0;

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $required = ['title','office','department','designation','marital_status','dob','pob','nationality','state','senatorial','lga','town','ward','religion','perm_address','contact_address','res_address','phone','gsm','email','blood_group','genotype','nok_name','nok_relation','nok_phone','nok_address','first_place_before','first_date_before','first_place_fpi','appt_type','post_appt','present_appt','regularization','gl','step','confirmation','first_appt_pub','qualifications','union_name','specialization','pfa','pin_code'];
    
    $errors = [];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucwords(str_replace('_', ' ', $field)) . " is required!";
        }
    }

    if (empty($errors)) {
        // Collect extra data
        $extra_data = [
            'promotion' => [],
            'education' => [],
            'academic' => [],
            'professional' => [],
            'publications' => []
        ];

        // Promotion
        if (!empty($_POST['promo_date'])) {
            foreach ($_POST['promo_date'] as $i => $date) {
                if (!empty($date)) {
                    $extra_data['promotion'][] = [
                        'date' => $date,
                        'from_position' => $_POST['from_position'][$i] ?? '',
                        'from_gl' => $_POST['from_gl'][$i] ?? '',
                        'from_step' => $_POST['from_step'][$i] ?? '',
                        'to_position' => $_POST['to_position'][$i] ?? '',
                        'to_gl' => $_POST['to_gl'][$i] ?? '',
                        'to_step' => $_POST['to_step'][$i] ?? ''
                    ];
                }
            }
        }

        // Education, Academic, Professional, Publications similar...

        $extra_json = json_encode($extra_data);

        $stmt = $conn->prepare("UPDATE save_biodata SET 
            title=?, office=?, department=?, designation=?, marital_status=?, dob=?, pob=?, nationality=?, state=?, senatorial=?, lga=?, town=?, ward=?, religion=?,
            perm_address=?, contact_address=?, res_address=?, phone=?, gsm=?, email=?, blood_group=?, genotype=?,
            nok_name=?, nok_relation=?, nok_phone=?, nok_address=?,
            first_place_before=?, first_date_before=?, first_place_fpi=?, appt_type=?, post_appt=?, present_appt=?, regularization=?,
            gl=?, step=?, confirmation=?, first_appt_pub=?, accommodation=?, qualifications=?, union_name=?, specialization=?, pfa=?, pin_code=?,
            extra_data=?, is_locked=1 WHERE staff_id=?");

        $stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssss", 
            $_POST['title'], $_POST['office'], $_POST['department'], $_POST['designation'], $_POST['marital_status'], $_POST['dob'], $_POST['pob'], $_POST['nationality'], $_POST['state'],
            $_POST['senatorial'], $_POST['lga'], $_POST['town'], $_POST['ward'], $_POST['religion'],
            $_POST['perm_address'], $_POST['contact_address'], $_POST['res_address'], $_POST['phone'], $_POST['gsm'], $_POST['email'],
            $_POST['blood_group'], $_POST['genotype'],
            $_POST['nok_name'], $_POST['nok_relation'], $_POST['nok_phone'], $_POST['nok_address'],
            $_POST['first_place_before'], $_POST['first_date_before'], $_POST['first_place_fpi'], $_POST['appt_type'], $_POST['post_appt'],
            $_POST['present_appt'], $_POST['regularization'], $_POST['gl'], $_POST['step'], $_POST['confirmation'], $_POST['first_appt_pub'],
            $_POST['accommodation'] ?? '', $_POST['qualifications'], $_POST['union_name'], $_POST['specialization'], $_POST['pfa'], $_POST['pin_code'],
            $extra_json, $staff_id
        );

        if ($stmt->execute()) {
            echo "<script>alert('Biodata & Records submitted and LOCKED successfully!'); location.reload();</script>";
        }
    } else {
        echo "<script>alert('Please fill all required fields!');</script>";
    }
}

$passport = $staff['passport'] && file_exists($staff['passport']) ? $staff['passport'] : 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Academic Staff Biodata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>

<style>
    body {
        background: linear-gradient(135deg, #001f3f, #003087);
        font-family: 'Times New Roman', serif;
        padding: 20px;
        margin: 0;
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 50px auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        overflow: hidden;
    }

    .header {
        background: #003087;
        color: white;
        padding: 40px;
        text-align: center;
    }

    .header h1 {
        font-size: 2.5rem;
        margin: 0;
        font-weight: bold;
    }

    .header h3, .header p {
        margin: 10px 0 0;
        opacity: 0.95;
    }

    /* Passport Box */
    .passport {
        width: 150px;
        height: 170px;
        border: 3px dashed #003087;
        margin: 20px auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f0f0;
        border-radius: 12px;
        overflow: hidden;
    }

    .passport img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Sections */
    .form-section {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 15px;
        margin: 20px 0;
    }

    .section-title, .section {
        background: #003087;
        color: white;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        font-size: 1.4rem;
        font-weight: bold;
        margin: 35px 0 20px;
    }

    .section {
        font-size: 1.5rem;
        padding: 18px;
        border-radius: 15px;
    }

    /* Form Elements */
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    .form-control:focus, .form-select:focus {
        border-color: #003087;
        box-shadow: 0 0 0 0.2rem rgba(0,48,135,0.25);
    }

    /* Buttons */
    .btn-submit {
        background: #28a745;
        color: white;
        padding: 20px 100px;
        font-size: 1.8rem;
        border-radius: 50px;
        font-weight: bold;
        border: none;
        box-shadow: 0 15px 40px rgba(40,167,69,0.5);
        transition: all 0.3s;
    }

    .btn-submit:hover {
        background: #218838;
        transform: translateY(-5px);
    }

    /* Alerts */
    .locked-alert {
        background: #d4edda;
        color: #155724;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        font-size: 1.4rem;
        font-weight: bold;
        border: 1px solid #c3e6cb;
    }

    .required {
        color: red;
        font-weight: bold;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container { margin: 20px auto; padding: 15px; }
        .header { padding: 30px 20px; }
        .header h1 { font-size: 2rem; }
        .btn-submit { padding: 15px 50px; font-size: 1.5rem; }
    }
</style></head>
<body>

<div class="container">
    <div class="header">
        <h1>THE FEDERAL POLYTECHNIC, ILE-OLUJI</h1>
        <h3>(Office of the Registrar)</h3>
        <h2>STAFF BIODATA FORM</h2>
        <p>Please fill in your information correctly</p>
    </div>

    <div class="p-4">
        <a href="non_academic_dashboard.php" class="btn btn-light btn-lg mb-4">
            ← Back to Dashboard
        </a>

        <?php if ($is_locked): ?>
            <div class="locked-alert">
                <h3>LOCKED & SUBMITTED</h3>
                <p>Your biodata has been submitted and locked permanently.</p>
                <a href="generate_pdf.php" class="btn btn-danger btn-lg">Download PDF</a>
            </div>
        <?php else: ?>
            <form method="post" enctype="multipart/form-data">
                <div class="text-center mb-4">
                    <div class="passport">
                        <img src="<?= htmlspecialchars($passport) ?>" alt="Passport" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
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
                            <input type="text" class="form-control" value="<?= htmlspecialchars($staff['sub_category']) ?>"readonly>
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

                <!-- PROMOTION RECORD -->
                <div class="section">PROMOTION / ADVANCEMENT RECORD</div>
                <div id="promotionRows">
                    <div class="row g-3 mb-3 align-items-center border-bottom pb-3">
                        <div class="col-md-1"><input type="date" class="form-control" name="promo_date[]"></div>
                        <div class="col-md-2"><input type="text" class="form-control" placeholder="From Position" name="from_position[]"></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="GL" name="from_gl[]"></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="Step" name="from_step[]"></div>
                        <div class="col-md-2"><input type="text" class="form-control" placeholder="To Position" name="to_position[]"></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="GL" name="to_gl[]"></div>
                        <div class="col-md-1"><input type="text" class="form-control" placeholder="Step" name="to_step[]"></div>
                        <div class="col-md-2"><button type="button" class="btn btn-success btn-sm" onclick="addRow('promotion')">+ Add Row</button></div>
                    </div>
                </div>

                <!-- EDUCATION -->
                <div class="section">EDUCATIONAL INSTITUTIONS ATTENDED</div>
                <div id="educationRows">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><input type="text" class="form-control" placeholder="School Name" name="school[]"></div>
                        <div class="col-md-2"><input type="number" class="form-control" placeholder="Year Admitted" name="admit[]"></div>
                        <div class="col-md-2"><input type="number" class="form-control" placeholder="Year Graduated" name="grad[]"></div>
                        <div class="col-md-2"><button type="button" class="btn btn-success btn-sm" onclick="addRow('education')">+ Add</button></div>
                    </div>
                </div>

                <!-- ACADEMIC QUALIFICATIONS -->
                <div class="section">ACADEMIC QUALIFICATIONS</div>
                <div id="academicRows">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><input type="text" class="form-control" placeholder="Certificate/Degree" name="acad_cert[]"></div>
                        <div class="col-md-2"><input type="date" class="form-control" name="acad_date[]"></div>
                        <div class="col-md-2"><input type="text" class="form-control" placeholder="Grade" name="acad_grade[]"></div>
                        <div class="col-md-3"><input type="text" class="form-control" placeholder="Awarding Body" name="acad_body[]"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-success btn-sm" onclick="addRow('academic')">+</button></div>
                    </div>
                </div>

                <!-- PROFESSIONAL QUALIFICATIONS -->
                <div class="section">PROFESSIONAL QUALIFICATIONS</div>
                <div id="profRows">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5"><input type="text" class="form-control" placeholder="Certificate" name="prof_cert[]"></div>
                        <div class="col-md-3"><input type="date" class="form-control" name="prof_date[]"></div>
                        <div class="col-md-3"><input type="text" class="form-control" placeholder="Awarding Body" name="prof_body[]"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-success btn-sm" onclick="addRow('prof')">+</button></div>
                    </div>
                </div>

                <!-- PUBLICATIONS -->
                <div class="section">PUBLICATIONS, JOURNALS & CONFERENCE PAPERS</div>
                <div id="publicationContainer">
                    <div class="border p-4 rounded mb-3 bg-light">
                        <div class="row g-3">
                            <div class="col-md-5"><input type="text" class="form-control" placeholder="Title of Article/Paper" name="title[]"></div>
                            <div class="col-md-3"><input type="text" class="form-control" placeholder="Journal / Conference Name" name="journal[]"></div>
                            <div class="col-md-2"><input type="number" class="form-control" placeholder="Year" name="pub_year[]"></div>
                            <div class="col-md-2"><input type="file" class="form-control" name="publication_files[]" accept=".pdf,.doc,.docx"></div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary mb-4" onclick="addPublication()">+ Add Another Publication</button>

                <div class="text-center mt-5 p-5 bg-danger bg-opacity-10 rounded border border-danger">
                    <h2 class="text-danger">FINAL SUBMISSION</h2>
                    <p class="lead">This action is IRREVERSIBLE. Your form will be LOCKED permanently.</p>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-lock"></i> SUBMIT & LOCK PERMANENTLY
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

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