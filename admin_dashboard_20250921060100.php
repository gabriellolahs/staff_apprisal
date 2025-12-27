<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// For now, let’s assume the admin is already logged in.
// Later, we’ll add authentication check here.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Staff Appraisal System</title>
    <style>
        body {
          margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    min-height: 50vh; /* Ensures the body takes at least the full viewport height */
    background-image: url('20250919_095448.jpg'); /* Specifies the background image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
    background-position:left; /* Centers the image horizontally and vertically */
    background-attachment: fixed; /* Keeps the background fixed during scroll */
    background-size: cover;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: #fff;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #f1c40f;
        }
        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        /* Main content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        footer {
            margin-top: 200px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }
        
    </style>
</head>
<body>
    <!-- Sidebar Menu -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">🏠 Dashboard</a>
        <a href="admin_add_staff.php">➕ Add Staff ID</a>
        <a href="manage_staff.php">👥 Manage Staff</a>
        <li><a href="manage_admins.php">Manage Admins</a></li>
        <a href="logout.php">🚪 Logout</a>
         <footer>
    <p>Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1">Thrive's Hub</a></p>
</footer>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h1>Welcome, Admin 👋</h1>
            <p>This is your dashboard. Use the menu on the left to manage the system.</p>
        </div>

        <footer>
            <p>Powered by Thrive's Hub</p>
        </footer>
    </div>
</body>
</html>
