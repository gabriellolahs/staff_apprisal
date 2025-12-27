<?php
// register_process.php
session_start();

// ✅ Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "staff_appraisal_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Get form values
$staff_id  = trim($_POST['staff_id']);
$surname   = trim($_POST['surname']);
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$gender    = trim($_POST['gender']);
$category  = trim($_POST['category']);
$sub_category = trim($_POST['sub_category']);

// ✅ Step 1: Check if Staff ID already exists
$check_sql = "SELECT * FROM staff WHERE staff_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('This Staff ID is already registered. Please login.'); window.location='login.php';</script>";
    exit();
}
$stmt->close();

// ✅ Step 2: Handle passport upload
$passport = "";
if (isset($_FILES['passport']) && $_FILES['passport']['error'] == 0) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Rename file with timestamp for uniqueness
    $fileName = time() . "_" . basename($_FILES["passport"]["name"]);
    $target_file = $target_dir . $fileName;

    if (move_uploaded_file($_FILES["passport"]["tmp_name"], $target_file)) {
        $passport = $fileName; // ✅ save only file name in DB
    }
}

// ✅ Step 3: Insert into staff table
$sql = "INSERT INTO staff (staff_id, surname, firstname, lastname, gender, category, sub_category, passport) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $staff_id, $surname, $firstname, $lastname, $gender, $category, $sub_category $passport);

if ($stmt->execute()) {
    // Save details into session
    $_SESSION['staff_id'] = $staff_id;
    $_SESSION['surname']  = $surname;
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['gender'] = $gender;
    $_SESSION['category'] = $category;
    $_SESSION['sub_category'] = $sub_category;
    $_SESSION['passport'] = $passport;
   

    // Redirect based on staff category
    if ($category === "Academic Staff") {
        header("Location: academic_dashboard.php");
    } else {
        header("Location: non_academic_dashboard.php");
    }
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
