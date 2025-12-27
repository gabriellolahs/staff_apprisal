<?php
session_start();
require_once "db.php";
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch Stats
$total_staff = $conn->query("SELECT COUNT(*) FROM staff")->fetch_row()[0];
$pending_appraisals = $conn->query("SELECT COUNT(DISTINCT staff_id) FROM staff_appraisal WHERE is_locked = 1 AND admin_approved = 0")->fetch_row()[0];
$approved_appraisals = $conn->query("SELECT COUNT(DISTINCT staff_id) FROM staff_appraisal WHERE admin_approved = 1")->fetch_row()[0];
$unlocked_appraisals = $conn->query("SELECT COUNT(DISTINCT staff_id) FROM staff_appraisal WHERE is_locked = 0 AND admin_approved = 0")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Staff Appraisal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003087;
            --secondary: #0056b3;
            --accent: #f1c40f;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #001f3f 0%, #003087 100%);
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 20px;
            box-shadow: 5px 0 15px rgba(0,0,0,0.4);
            z-index: 1000;
        }
        .sidebar .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .logo img {
            width: 160px;
            height: auto;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.5));
        }
        .sidebar h2 {
            text-align: center;
            color: var(--accent);
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin: 20px 0;
        }
        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 12px;
            margin: 8px 0;
            transition: all 0.3s;
            font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #34495e;
            color: var(--accent);
            transform: translateX(10px);
        }
        .sidebar a i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }
        .main-content {
            margin-left: 280px;
            padding: 40px;
            color: white;
        }
        .welcome-header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin: 0;
            color: white;
        }
        .welcome-header p {
            font-size: 1.3rem;
            opacity: 0.9;
            margin: 10px 0 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .stat-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--accent);
        }
        .stat-card h3 {
            font-size: 2.5rem;
            margin: 10px 0;
            color: white;
        }
        .stat-card p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .quick-actions {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .quick-actions h3 {
            color: var(--accent);
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        .btn-action {
            display: block;
            width: 100%;
            padding: 18px;
            margin: 15px 0;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-review { background: #28a745; color: white; }
        .btn-unlock { background: #fd7e14; color: white; }
        .btn-add { background: #007bff; color: white; }
        .btn-manage { background: #6f42c1; color: white; }
        .btn-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }
        footer {
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-size: 0.9rem;
            margin-top: 50px;
        }
        footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: bold;
        }
        @media (max-width: 992px) {
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: relative; 
                padding-bottom: 0;
            }
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar a { display: inline-block; width: auto; margin: 5px; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="20250406_220923_transcpr-removebg-preview.png" alt="Logo">
    </div>
    <h2>ADMIN PANEL</h2>
    <a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="admin_review.php"><i class="fas fa-clipboard-check"></i> Review Appraisals</a>
    <a href="unlock_appraisal.php"><i class="fas fa-unlock"></i> Unlock Appraisals</a>
    <a href="admin_add_staff.php"><i class="fas fa-user-plus"></i> Add Staff</a>
    <a href=" admin_edit_biodata.php"><i class="fas fa-user-plus"></i> edit biodata </a>
    <a href="manage_staff.php"><i class="fas fa-users-cog"></i> Manage Staff</a>
    <a href="manage_admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="welcome-header">
        <h1>Office of the Registrar</h1>
        <p>Federal Polytechnic Ile-Oluji • Staff Appraisal Management System</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3><?= $total_staff ?></h3>
            <p>Total Staff</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock"></i>
            <h3><?= $pending_appraisals ?></h3>
            <p>Pending Review</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <h3><?= $approved_appraisals ?></h3>
            <p>Approved</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-unlock"></i>
            <h3><?= $unlocked_appraisals ?></h3>
            <p>Unlocked (Editable)</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="row g-3">
            <div class="col-md-3">
                <a href="admin_review.php" class="btn-action btn-review">
                    <i class="fas fa-clipboard-check fa-2x"></i><br>
                    Review & Approve
                </a>
            </div>
            <div class="col-md-3">
                <a href="unlock_biodata.php" class="btn-action btn-unlock">
                    <i class="fas fa-unlock fa-2x"></i><br>
                    Unlock Biodata
                </a>
            </div>
            <div class="col-md-3">
                <a href="admin_add_staff.php" class="btn-action btn-add">
                    <i class="fas fa-user-plus fa-2x"></i><br>
                    Add New Staff
                </a>
            </div>
            <div class="col-md-3">
                <a href="manage_staff.php" class="btn-action btn-manage">
                    <i class="fas fa-users-cog fa-2x"></i><br>
                    Manage Staff
                </a>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Federal Polytechnic Ile-Oluji • All Rights Reserved</p>
        <p>Powered by <a href="#">Thrive's Hub</a></p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>