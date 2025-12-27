<?php
session_start();
include("db.php");

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current'];
    $new     = $_POST['new'];
    $confirm = $_POST['confirm'];

    $stmt = $conn->prepare("SELECT password FROM staff WHERE staff_id = ?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();

    if ($staff && password_verify($current, $staff['password'])) {
        if ($new === $confirm) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE staff SET password=? WHERE staff_id=?");
            $update->bind_param("ss", $hashed, $staff_id);
            if ($update->execute()) {
                echo "<script>alert('Password updated successfully!'); window.location='dashboard.php';</script>";
            }
        } else {
            echo "<script>alert('New passwords do not match!');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin:0; font-family: Arial, sans-serif; display:flex; height:100vh; }
        .sidebar { width:220px; background:#2c3e50; color:white; padding:20px; }
        .sidebar h2 { font-size:20px; margin-bottom:20px; }
        .sidebar a { display:block; color:white; padding:10px; text-decoration:none; border-radius:5px; }
        .sidebar a:hover { background:#34495e; }
        .main-content { flex-grow:1; background:#f4f6f9; padding:20px; }
        .card { max-width:500px; margin:auto; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1); }
        .btn-custom { background:#28a745; color:white; border-radius:10px; }
        .btn-custom:hover { background:#1e7e34; }
         .logo img {
            width: 150px;
            height: auto;
        }
         .main {
            max-width: 600px; margin: auto; background: #fff; padding: 25px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: margin-left 0.3s;
           position: center;
        }
        .main.shift {
            margin-left: 260px;
        }

        .details {
            flex: 2;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-right: 20px;
        }
        .details h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .details p {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }
        .details strong {
            color: #2c3e50;
        }
        footer {
            text-align: center;
            padding: 10px;
            background: #2c3e50;
            color: #fff;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
         <div class="logo">
            <center>
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="System Logo">
    </center>
        <h2>Academic Menu</h2>
        <a href="academic_dashboard.php">🏠 Dashboard</a>
        <a href="academic_profile.php">👤 Profile</a>
        <a href="academic_appraisal.php">📝 Appraisal</a>
        <a href="academic_reports.php">📊 Reports</a>
        <a href="set_password.php">Set Password</a>
        
        <a href="logout.php">Logout</a>
    </div>
    </div>
<div class="main" id="main">
        <div class="details">
    <!-- Main Content -->
    <div class="main-content">
        <h3>🔐 Change Your Password</h3>
        <div class="card p-4 bg-white">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">Update Password</button>
                <a href="lset_password.php" class="btn-back">Back</a>
            </form>
        </div>
        </div>
    </div>
    <footer>
        Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1" target="_blank" style="color:#1abc9c;">Thrive's Hub</a>
    </footer>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("main").classList.toggle("shift");
</body>
</html>
