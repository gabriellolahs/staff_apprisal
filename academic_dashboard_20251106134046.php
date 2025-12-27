<?php
session_start();
include("db.php"); 

// Redirect to login if not logged in
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}
$surname = $_SESSION['surname'];
$staff_id = $_SESSION['staff'];

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Handle passport path
$passport = "uploads/default.png"; // default image
if (!empty($staff['passport']) && file_exists($staff['passport'])) {
    $passport = $staff['passport']; // already stored with path (e.g., uploads/12345_passport.jpg)
} elseif (!empty($staff['passport']) && file_exists("uploads/" . $staff['passport'])) {
    $passport = "uploads/" . $staff['passport']; // if only filename was saved
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Academic Staff Dashboard</title>
    <style>
        body {
            margin: 0;
    font-family: Arial, sans-serif;
    min-height: 50vh; /* Ensures the body takes at least the full viewport height */
    background-image: url('20250919_095448.jpg'); /* Specifies the background image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
    background-position:left; /* Centers the image horizontally and vertically */
    background-attachment: fixed; /* Keeps the background fixed during scroll */
    background-size: cover;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #2c3e50;
            color: #fff;
            position: fixed;
            top: 0;
            left: -260px;
            padding: 20px;
            transition: 0.3s;
            z-index: 1000;
        }
        .sidebar.active {
            left: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ecf0f1;
        }
        .sidebar a {
            display: block;
            color: #ecf0f1;
            padding: 10px;
            text-decoration: none;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: #34495e;
        }

        /* Toggle Button */
        .toggle-btn {
            position: fixed;
            left: 15px;
            top: 15px;
            font-size: 24px;
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1100;
        }

        /* Main Content */
        .main {
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: margin-left 0.3s;
            margin-left: 0;
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
        .passport {
            flex: 1;
            text-align: center;
        }
        .passport img {
            width: 180px;
            height: 180px;
            border-radius: 10px;
            border: 3px solid #2980b9;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
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

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main.shift {
                margin-left: 0;
            }
        }
         .logo img {
            width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <div class="sidebar" id="sidebar">
         <div class="logo">
            <center>
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="System Logo">
    </center>
    </div>
    <h2>Teaching Menu</h2>
    <a href="academic_dashboard.php">🏠 Dashboard</a>
    <a href="biodata.php">👤 Profile</a>
    <a href="staff_appraisal.php">📝 Appraisal</a>
    <a href="academic_reports.php">📊 Reports</a>
   <a href="set_password.php">Set Password</a>
    <a href="logout.php" style="color: #e74c3c;">🚪 Logout</a>
    </div>

    <div class="main" id="main">
        <div class="details">
                <h2>Welcome, <?php echo htmlspecialchars($surname); ?>!</h2>
                <p><strong>Your Staff ID:</strong> <strong><?php echo htmlspecialchars($staff_id); ?></strong></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($staff['category']); ?></p>
            <p>Select an option from the menu to continue.</p>
            <a href="biodata.php?staff_id=<?php echo $staff_id; ?>" class="btn btn-primary">View Biodata</a>

        </div>
        <div class="passport">
            <img src="<?php echo htmlspecialchars($passport); ?>" alt="Passport">
        </div>
    </div>

    <footer>
        Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1" target="_blank" style="color:#1abc9c;">Thrive's Hub</a>
    </footer>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("main").classList.toggle("shift");
        }
    </script>
</body>
</html>
