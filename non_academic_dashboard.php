<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if (!$staff) {
    die("Staff record not found.");
}

// Handle passport
$passport = "uploads/default.png";
if (!empty($staff['passport']) && file_exists($staff['passport'])) {
    $passport = $staff['passport'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Academic Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003087;
            --secondary: #0056b3;
            --accent: #f1c40f;
            --light: #f8f9fa;
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
            transition: all 0.3s;
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
        .welcome-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .welcome-card h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin: 0;
            color: white;
        }
        .welcome-card p {
            font-size: 1.4rem;
            opacity: 0.9;
            margin: 15px 0 0;
        }
        .profile-card {
            background: white;
            color: #333;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
        }
        .profile-photo {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 20px;
            border: 8px solid #003087;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .profile-info h2 {
            font-size: 2.2rem;
            margin: 0;
            color: #003087;
        }
        .profile-info p {
            margin: 8px 0;
            font-size: 1.1rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .info-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .info-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .info-card h3 {
            font-size: 1.4rem;
            margin: 15px 0 10px;
            color: #003087;
        }
        .info-card a {
            display: block;
            background: var(--primary);
            color: white;
            padding: 15px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            transition: all 0.3s;
        }
        .info-card a:hover {
            background: var(--secondary);
            transform: scale(1.05);
        }
        footer {
            text-align: center;
            padding: 30px;
            color: #ddd;
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
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="20250406_220923_transcpr-removebg-preview.png" alt="Logo">
    </div>
    <h2>NON-TEACHING MENU</h2>
    <a href="non_academic_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="non_biodata.php"><i class="fas fa-id-card"></i> My Profile</a>
    <a href="non_academic_appraisal.php"><i class="fas fa-clipboard-list"></i> Annual Appraisal</a>
    <a href="non_academic_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="non_set_password.php"><i class="fas fa-key"></i> Change Password</a>
    <a href="logout.php" style="color:#e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="welcome-card">
        <h1>Welcome Back, <strong><?= strtoupper($staff['surname'] . ' ' . $staff['firstname']) ?></strong>!</h1>
        <p>Non-Academic Staff Portal • Federal Polytechnic Ile-Oluji</p>
    </div>

    <div class="profile-card">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($passport) ?>" class="profile-photo" alt="Passport">
            <div class="profile-info">
                <h2><?= strtoupper($staff['surname'] . ' ' . $staff['firstname'] . ' ' . ($staff['lastname'] ?? '')) ?></h2>
                <p><strong>Staff ID: <?= $staff['staff_id'] ?></strong></p>
                <p><strong>Category: <?= $staff['category'] ?? 'N/A' ?></strong></p>
                <p><strong>Gender:  <?= htmlspecialchars($staff['gender']) ?></strong></p>
                <p><strong><?= strtoupper($staff['sub_category']) ?></strong></p>               
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <i class="fas fa-id-card"></i>
            <h3>My Profile</h3>
            <p>View and update your personal information</p>
            <a href="non_biodata.php">Go to Profile</a>
        </div>
        <div class="info-card">
            <i class="fas fa-clipboard-list"></i>
            <h3>Annual Appraisal</h3>
            <p>Complete your performance appraisal form</p>
            <a href="non_academic_appraisal.php">Start Appraisal</a>
        </div>
        <div class="info-card">
            <i class="fas fa-chart-bar"></i>
            <h3>Reports</h3>
            <p>View your appraisal history and status</p>
            <a href="non_academic_reports.php">View Reports</a>
        </div>
        <div class="info-card">
            <i class="fas fa-key"></i>
            <h3>Security</h3>
            <p>Set or change your login password</p>
            <a href="non_set_password.php">Change Password</a>
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