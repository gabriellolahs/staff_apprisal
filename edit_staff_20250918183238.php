<?php
// edit_staff.php
session_start();

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "staff_appraisal_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get staff details
if (!isset($_GET['id'])) {
    echo "<script>alert('No staff selected.'); window.location='manage_staff.php';</script>";
    exit();
}

$id = intval($_GET['id']);
$staff = $conn->query("SELECT * FROM staff WHERE id = $id")->fetch_assoc();

if (!$staff) {
    echo "<script>alert('Staff not found.'); window.location='manage_staff.php';</script>";
    exit();
}

// Update staff record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $surname   = trim($_POST['surname']);
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $age       = intval($_POST['age']);

    // Handle passport upload (optional)
    $passport = $staff['passport']; // keep old one by default
    if (!empty($_FILES['passport']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES['passport']['name']);
        if (move_uploaded_file($_FILES['passport']['tmp_name'], $target_file)) {
            $passport = $target_file;
        }
    }

    $sql = "UPDATE staff SET surname=?, firstname=?, lastname=?, age=?, passport=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $surname, $firstname, $lastname, $age, $passport, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Staff updated successfully!'); window.location='manage_staff.php';</script>";
    } else {
        echo "<script>alert('Error updating staff.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff - Admin</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
        }
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
            max-width: 600px;
        }
        input[type="text"], input[type="number"], input[type="file"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #2980b9;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #1c5d82;
        }
        img {
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">🏠 Dashboard</a>
        <a href="admin_add_staff.php">➕ Add Staff ID</a>
        <a href="manage_staff.php">👥 Manage Staff</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h1>✏️ Edit Staff</h1>
            <form method="POST" enctype="multipart/form-data">
                <label>Surname</label><br>
                <input type="text" name="surname" value="<?php echo htmlspecialchars($staff['surname']); ?>" required><br>

                <label>First Name</label><br>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($staff['firstname']); ?>" required><br>

                <label>Last Name</label><br>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($staff['lastname']); ?>"><br>

                <label>Age</label><br>
                <input type="number" name="age" value="<?php echo $staff['age']; ?>" required><br>

                <label>Current Passport</label><br>
                <img src="<?php echo $staff['passport']; ?>" alt="passport" width="100" height="100"><br>

                <label>Upload New Passport (optional)</label><br>
                <input type="file" name="passport"><br>

                <button type="submit">Update Staff</button>
            </form>
        </div>
    </div>
</body>
</html>
