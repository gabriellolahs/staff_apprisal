<?php
session_start();
include("db_connect.php"); // Your DB connection file

$staff_id = $_SESSION['staff_id']; // Staff logged in
// Fetch biodata
$sql = "SELECT * FROM staff_biodata WHERE staff_id='$staff_id'";
$result = mysqli_query($conn, $sql);
$biodata = mysqli_fetch_assoc($result);

// Check lock status
if ($biodata && $biodata['locked'] == 1) {
    echo "<h2>Your biodata is locked. Contact Admin to unlock.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Biodata Form</title>
  <style>
    .tab { display: none; }
    .step { height: 20px; width: 20px; margin: 0 2px; background-color: #bbbbbb; border: none; border-radius: 50%; display: inline-block; opacity: 0.5; }
    .step.active { opacity: 1; }
    .step.finish { background-color: #04AA6D; }
  </style>
</head>
<body>
<h2>Staff Biodata Form</h2>
<form id="regForm" action="save_biodata.php" method="post">

  <!-- Step 1 -->
  <div class="tab">
    <h3>Personal Information</h3>
    Surname: <input type="text" name="surname" value="<?= $biodata['surname'] ?? '' ?>"><br>
    First Name: <input type="text" name="firstname" value="<?= $biodata['firstname'] ?? '' ?>"><br>
    Middle Name: <input type="text" name="middlename" value="<?= $biodata['middlename'] ?? '' ?>"><br>
    Gender: <select name="gender">
        <option value="">--Select--</option>
        <option value="Male" <?= ($biodata['gender']??'')=='Male'?'selected':'' ?>>Male</option>
        <option value="Female" <?= ($biodata['gender']??'')=='Female'?'selected':'' ?>>Female</option>
    </select><br>
    DOB: <input type="date" name="dob" value="<?= $biodata['dob'] ?? '' ?>"><br>
  </div>

  <!-- Step 2 -->
  <div class="tab">
    <h3>Contact Information</h3>
    Permanent Address: <input type="text" name="permanent_address" value="<?= $biodata['permanent_address'] ?? '' ?>"><br>
    Phone: <input type="text" name="phone" value="<?= $biodata['phone'] ?? '' ?>"><br>
    Email: <input type="email" name="email" value="<?= $biodata['email'] ?? '' ?>"><br>
  </div>

  <!-- Step 3 -->
  <div class="tab">
    <h3>Next of Kin</h3>
    Name: <input type="text" name="nok_name" value="<?= $biodata['nok_name'] ?? '' ?>"><br>
    Relationship: <input type="text" name="nok_relationship" value="<?= $biodata['nok_relationship'] ?? '' ?>"><br>
    Phone: <input type="text" name="nok_phone" value="<?= $biodata['nok_phone'] ?? '' ?>"><br>
  </div>

  <!-- Step 4 -->
  <div class="tab">
    <h3>Official Information</h3>
    Date of First Appointment: <input type="date" name="first_appt_date" value="<?= $biodata['first_appt_date'] ?? '' ?>"><br>
    Grade Level: <input type="text" name="grade_level" value="<?= $biodata['grade_level'] ?? '' ?>"><br>
    Union: 
    <select name="union_name">
      <option value="">--Select--</option>
      <option value="ASUP" <?= ($biodata['union_name']??'')=='ASUP'?'selected':'' ?>>ASUP</option>
      <option value="SSANIP" <?= ($biodata['union_name']??'')=='SSANIP'?'selected':'' ?>>SSANIP</option>
      <option value="NASU" <?= ($biodata['union_name']??'')=='NASU'?'selected':'' ?>>NASU</option>
    </select><br>
  </div>

  <!-- Navigation -->
  <div style="overflow:auto;">
    <div style="float:right;">
      <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
    </div>
  </div>

  <!-- Circles -->
  <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
  </div>

</form>

<script>
var currentTab = 0;
showTab(currentTab);

function showTab(n) {
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  if (n == 0) { document.getElementById("prevBtn").style.display = "none"; } 
  else { document.getElementById("prevBtn").style.display = "inline"; }
  if (n == (x.length - 1)) { document.getElementById("nextBtn").innerHTML = "Submit"; } 
  else { document.getElementById("nextBtn").innerHTML = "Next"; }
  fixStepIndicator(n)
}

function nextPrev(n) {
  var x = document.getElementsByClassName("tab");
  if (n == 1 && !validateForm()) return false;
  x[currentTab].style.display = "none";
  currentTab = currentTab + n;
  if (currentTab >= x.length) {
    document.getElementById("regForm").submit();
    return false;
  }
  showTab(currentTab);
}

function validateForm() {
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  for (i = 0; i < y.length; i++) {
    if (y[i].value == "") { y[i].className += " invalid"; valid = false; }
  }
  if (valid) { document.getElementsByClassName("step")[currentTab].className += " finish"; }
  return valid;
}

function fixStepIndicator(n) {
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) { x[i].className = x[i].className.replace(" active", ""); }
  x[n].className += " active";
}
</script>
</body>
</html>
