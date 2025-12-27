<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "staff_appraisal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$staff_id = $_GET['staff_id'];

$sql = "SELECT * FROM staff_biodata WHERE staff_id = '$staff_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<h3 style='color:red;'>No biodata found for this staff.</h3>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Staff Biodata</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        input[readonly], textarea[readonly] {
            background-color: #f3f3f3;
            cursor: not-allowed;
        }
        body {
            margin: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">Staff Biodata (View Only)</h2>
    <form>
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Title</label>
                <input type="text" class="form-control" value="<?php echo $row['title']; ?>" readonly>
            </div>
            <div class="col-md-4">
                <label>Surname</label>
                <input type="text" class="form-control" value="<?php echo $row['surname']; ?>" readonly>
            </div>
            <div class="col-md-4">
                <label>Firstname</label>
                <input type="text" class="form-control" value="<?php echo $row['firstname']; ?>" readonly>
            </div>
        </div>

        <!-- Example of more fields -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label>School</label>
                <input type="text" class="form-control" value="<?php echo $row['school']; ?>" readonly>
            </div>
            <div class="col-md-6">
                <label>Department</label>
                <input type="text" class="form-control" value="<?php echo $row['department']; ?>" readonly>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Email</label>
                <input type="text" class="form-control" value="<?php echo $row['email']; ?>" readonly>
            </div>
            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" class="form-control" value="<?php echo $row['phone']; ?>" readonly>
            </div>
        </div>

        <!-- Add all other fields similarly... -->

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-success">Return to Dashboard</a>
        </div>
    </form>
</div>
</body>
</html>
