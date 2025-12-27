<?php
$host = "localhost";
$user = "root";   // default for XAMPP
$pass = "";       // default is empty
$db   = "staff_appraisal";
$db   = "staff_appraisal_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
