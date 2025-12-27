<?php
session_start();
include("db.php");

// ✅ Redirect if not logged in
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

// ✅ Fetch staff info
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .profile-box {
            max-width: 600px; margin: auto; background: #fff; padding: 25px;
            border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .profile-box h2 { text-align: center; color: #2c3e50; }
        .profile-box p { font-size: 16px; margin: 10px 0; }
        img { width: 150px; height: 150px; border-radius: 10px; object-fit: cover; display: block; margin: 15px auto; border: 3px solid #2980b9; }
        a { display: block; text-align: center; margin-top: 15px; color: #2980b9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="profile-box">
        <h2>Staff Profile</h2>
        <img src="<?php echo htmlspecialchars($passport); ?>" alt="Passport">
        <p><strong>Staff ID:</strong> <?php echo $staff['staff_id']; ?></p>
        <p><strong>Name:</strong> <?php echo $staff['surname'].' '.$staff['firstname'].' '.$staff['lastname']; ?></p>
        <p><strong>Gender:</strong> <?php echo $staff['gender']; ?></p>
        <p><strong>Category:</strong> <?php echo $staff['category']; ?></p>
        <a href="academic_dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
