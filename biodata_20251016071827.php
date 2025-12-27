<?php
session_start();
include "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

// Fetch staff data
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Default values
$is_locked = 0;
$readonly = '';
$disabled = '';

$passport = "uploads/default.png"; 
if (!empty($staff['passport']) && file_exists($staff['passport'])) {
    $passport = $staff['passport']; 
} elseif (!empty($staff['passport']) && file_exists("uploads/" . $staff['passport'])) {
    $passport = "uploads/" . $staff['passport']; 
}


// Fetch biodata
$sql = "
SELECT sb.*, s.school_name, d.department_name
FROM staff_biodata sb
LEFT JOIN schools s ON sb.school = s.id
LEFT JOIN departments d ON sb.department = d.id
WHERE sb.staff_id = ?
";
$biodata['school_name']
$biodata['department_name']

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $biodata = $result->fetch_assoc();
    $is_locked = isset($biodata['is_locked']) ? $biodata['is_locked'] : 0;
} else {
    $biodata = [];
}

if ($is_locked) {
    $readonly = 'readonly';
    $disabled = 'disabled';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Biodata Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
     body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-image: url('20250919_095448.jpg');
        background-repeat: no-repeat;
        background-position: left;
        background-attachment: fixed;
        background-size: cover;
     }
    .form-container {
      max-width: 1100px; margin: 30px auto;
      background: white; padding: 25px;
      border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .header-box {
      display: flex; justify-content: space-between; align-items: center;
      margin: 5px;
    }
    .header-box img { width: 120px; height: 120px; border-radius: 6px; object-fit: cover; }
    h3,h4 { color: #2c3e50; margin-bottom: 10px; }
    label { font-weight: 600; }
    .btn-primary { background: #0056b3; border: none; border-radius: 8px; padding: 10px 20px; }
    .btn-primary:hover { background: #003d82; }
    .section-title { background: #e9ecef; padding: 10px; border-radius: 6px; margin-top: 25px; }
    .sidebar { width: 240px; height: 100vh; background: #2c3e50; color: #fff; position: fixed; top: 0; left: 0; padding: 20px; z-index: 1000; }
    .sidebar h2 { text-align: center; margin-bottom: 30px; color: #f1c40f; }
    .sidebar a { display: block; color: #ecf0f1; padding: 10px; text-decoration: none; margin: 5px 0; border-radius: 5px; }
    .sidebar a:hover { background: #34495e; }
    .logo img { width: 150px; height: auto; }
    .main { margin-left: 240px; padding: 30px; transition: margin-left 0.3s; }
    @media (max-width: 768px) { .main.shift { margin-left: 0; } }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="logo text-center mb-3">
    <img src="20250406_220923_transcpr-removebg-preview.png" alt="System Logo">
  </div>
  <h2>Teaching Menu</h2>
  <a href="academic_dashboard.php">🏠 Dashboard</a>
  <a href="biodata.php">👤 Profile</a>
  <a href="academic_appraisal.php">📝 Appraisal</a>
  <a href="academic_reports.php">📊 Reports</a>
  <a href="set_password.php">🔑 Set Password</a>
  <a href="logout.php" style="color:#e74c3c;">🚪 Logout</a>
</div>

<div class="main">
  <div class="header-box">
    <div class="text-center">
      <h3>THE FEDERAL POLYTECHNIC, ILE-OLUJI</h3>
      <h5>(Office of the Registrar)</h5>
      <h4>STAFF BIODATA FORM</h4>
    </div>
    <img src="<?php echo htmlspecialchars($passport); ?>" alt="Passport">
  </div>

  
  <form method="post" action="save_biodata.php" class="form-container">
    <h6><i>Please fill your information correctly</i></h6>
    <?php if ($is_locked): ?>
    <div class="alert alert-info text-center mt-4">
      Your biodata has been locked. Contact the admin to make any changes.
    </div>
  <?php endif; ?>

    <!-- Staff Info -->
    <div class="section-title">Staff Information</div>
    <div class="row mb-3">
      <div class="col-md-3">
        <label>Staff ID Number</label>
        <input type="text" class="form-control" name="staff_id" value="<?php echo $staff['staff_id']; ?>" readonly>
      </div>
      <div class="col-md-3">
        <label>Category</label>
        <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($staff['category']); ?>" readonly>
      </div>
      <div class="col-md-3">
        <label>School</label>
        <select class="form-control" id="school" name="school" <?php echo $disabled; ?>>
          <option value="">-- Select School --</option>
          <?php
          $result = $conn->query("SELECT * FROM schools ORDER BY school_name ASC");
          while($row = $result->fetch_assoc()){
            $selected = ($staff['school_id'] == $row['id']) ? "selected" : "";
            echo "<option value='".$row['id']."' $selected>".$row['school_name']."</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-3">
        <label>Department</label>
        <select class="form-control" id="department" name="department" <?php echo $disabled; ?>>
          <option value="">-- Select Department --</option>
        </select>
      </div>
      <div class="col-md-3">
        <label>Designation/Present Post</label><input type="text" class="form-control" name="designation" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>

    <!-- Personal Info -->
    <div class="section-title">Personal Information</div>
    <div class="row mb-3">
      <div class="col-md-3"><label>Title</label><input type="text" class="form-control" name="title" <?php echo $readonly; ?>required></div>
      <div class="col-md-3"><label>Surname</label><input type="text" class="form-control" name="surname" value="<?php echo $staff['surname']; ?>" <?php echo $readonly; ?>></div>
      <div class="col-md-3"><label>First Name</label><input type="text" class="form-control" name="firstname" value="<?php echo $staff['firstname']; ?>" <?php echo $readonly; ?>></div>
      <div class="col-md-3"><label>Middle Name</label><input type="text" class="form-control" name="lastname" value="<?php echo $staff['lastname']; ?>" <?php echo $readonly; ?>></div>
    </div>

<div class="row mb-3">
      <div class="col-md-3"><label>Marital Status</label><input type="text" class="form-control" name="marital_status"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Sex</label><input type="text" class="form-control" name="sex" value="<?php echo $staff['gender']; ?>"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Date of Birth</label><input type="date" class="form-control" name="dob"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Place of Birth</label><input type="text" class="form-control" name="pob"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3"><label>Nationality</label><input type="text" class="form-control" name="nationality"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>State of Origin</label><input type="text" class="form-control" name="state"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Senatorial District</label><input type="text" class="form-control" name="district"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>LGA of Origin</label><input type="text" class="form-control" name="lga"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3"><label>Town of Origin</label><input type="text" class="form-control" name="town"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Ward</label><input type="text" class="form-control" name="ward"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Religion</label><input type="text" class="form-control" name="religion"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Hobbies</label><input type="text" class="form-control" name="hobbies"<?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>

    <!-- Contact Info -->
    <div class="section-title">Contact Information</div>
    <div class="row mb-3">
      <div class="col-md-4"><label>Permanent Address</label><input type="text" class="form-control" name="perm_address" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Contact Address</label><input type="text" class="form-control" name="contact_address" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Residential Address</label><input type="text" class="form-control" name="res_address" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>
    <div class="row mb-3">
      <div class="col-md-3"><label>Phone</label><input type="text" class="form-control" name="phone" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>GSM</label><input type="text" class="form-control" name="gsm" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Email</label><input type="email" class="form-control" name="email" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Blood Group</label><input type="text" class="form-control" name="blood_group" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>
    <div class="row mb-3">
      <div class="col-md-3"><label>Genotype</label><input type="text" class="form-control" name="genotype" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Spouse Name</label><input type="text" class="form-control" name="spouse_name" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>No. of Children</label><input type="number" class="form-control" name="children" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>

    <!-- Next of Kin -->
    <div class="section-title">Next of Kin</div>
    <div class="row mb-3">
      <div class="col-md-4"><label>Name</label><input type="text" class="form-control" name="nok_name" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Relationship</label><input type="text" class="form-control" name="nok_relation" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Phone</label><input type="text" class="form-control" name="nok_phone" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>
    <div class="mb-3"><label>Address</label><input type="text" class="form-control" name="nok_address" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>

    <!-- Official Info -->
    <div class="section-title">Official Information</div>
    <div class="row mb-3">
      <div class="col-md-4"><label>Place of First Appt (before FPI)</label><input type="text" class="form-control" name="first_place_before" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Date of First Appt (before FPI)</label><input type="date" class="form-control" name="first_date_before" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Place of First Appt (in FPI)</label><input type="text" class="form-control" name="first_place_fpi" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>
    <div class="row mb-3">
      <div class="col-md-4"><label>Type of Appointment</label><input type="text" class="form-control" name="appt_type" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Post On Appt (in FPI)</label><input type="text" class="form-control" name="post_appt" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-4"><label>Office Held</label><input type="text" class="form-control" name="office_held" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>
    <div class="row mb-3">
      <div class="col-md-3"><label>Date of Present Appt</label><input type="date" class="form-control" name="present_appt" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Date of Regularization</label><input type="date" class="form-control" name="regularization" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>GL</label><input type="text" class="form-control" name="gl" <?php echo $is_locked ? 'readonly' : ''; ?>></div>
      <div class="col-md-3"><label>Step</label><input type="text" class="form-control" name="step" <?php echo $is_locked ? 'readonly' : ''; ?>></div>
    </div>
    <div class="row mb-3">
      <div class="col-md-3"><label>Date of Confirmation</label><input type="date" class="form-control" name="confirmation" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Date of First Appt (in Public Service)</label><input type="date" class="form-control" name="first_appt_pub" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
      <div class="col-md-3"><label>Accommodation</label><input type="text" class="form-control" name="accommodation" <?php echo $is_locked ? 'readonly' : ''; ?>required></div>
    </div>

    <!-- Extra Info -->
    <div class="section-title">Additional Information</div>
<div class="row mb-3">
  <div class="col-md-4">
    <label>Qualifications</label>
    <input type="text" class="form-control" name="qualifications"<?php echo $is_locked ? 'readonly' : ''; ?> required>
  </div>
  <div class="col-md-4">
    <label>Union Name</label>
    <input type="text" class="form-control" name="union_name" <?php echo $is_locked ? 'readonly' : ''; ?> required>
  </div>
  <div class="col-md-4">
    <label>Specialization</label>
    <input type="text" class="form-control" name="specialization" <?php echo $is_locked ? 'readonly' : ''; ?> required>
  </div>
</div>
<div class="row mb-3">
  <div class="col-md-6">
    <label>Pension Fund Administrator</label>
    <input type="text" class="form-control" name="pfa" <?php echo $is_locked ? 'readonly' : ''; ?> required>
  </div>
  <div class="col-md-6">
    <label>Pin Code</label>
    <input type="text" class="form-control" name="pin_code" <?php echo $is_locked ? 'readonly' : ''; ?> required>
  </div>
</div> 

    <?php if (!$is_locked): ?>
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">Save Biodata</button>
      </div>
    <?php endif; ?>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Load departments dynamically
  $("#school").change(function(){
    var school_id = $(this).val();
    $.ajax({
      url: "get_departments.php",
      type: "POST",
      data: { school_id: school_id },
      success: function(data){
        $("#department").html(data);
      }
    });
  });

  // Auto-load staff’s current department
  $(document).ready(function(){
    var school_id = $("#school").val();
    var current_dept = "<?php echo $staff['department_id'] ?? ''; ?>";
    if(school_id){
      $.ajax({
        url: "get_departments.php",
        type: "POST",
        data: { school_id: school_id },
        success: function(data){
          $("#department").html(data);
          if(current_dept){
            $("#department").val(current_dept);
          }
        }
      });
    }
  });
</script>
</body>
</html>
