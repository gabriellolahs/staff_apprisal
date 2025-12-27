<?php
// admin_add_staff.php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = trim($_POST['staff_id']);

    if (!empty($staff_id)) {
        // Check if staff ID already exists
        $check_sql = "SELECT * FROM allowed_staff WHERE staff_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Staff ID already exists!');</script>";
        } else {
            // Insert new staff ID
            $sql = "INSERT INTO allowed_staff (staff_id) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $staff_id);

            if ($stmt->execute()) {
                echo "<script>alert('Staff ID added successfully!'); window.location='admin_add_staff.php';</script>";
            } else {
                echo "<script>alert('Error adding Staff ID.');</script>";
            }
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please enter a Staff ID!');</script>";
    }
}

// Fetch all allowed staff IDs
$staff_ids = $conn->query("SELECT * FROM allowed_staff ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff ID - Admin</title>
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
        }
        input[type="text"] {
            width: 80%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #27ae60;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #219150;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background: #2c3e50;
            color: white;
        }
        footer {
            margin-top: 200px;
            text-align: center;
            font-size: 14px;
            color: #f1c40f;
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
        <footer>
            <p>Powered by Thrive's Multiplex Enterprise</p>
        </footer>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h1>➕ Add Staff ID</h1>
            <form method="POST">
                <input type="text" name="staff_id" placeholder="Enter Staff ID" required>
                <button type="submit">Add</button>
            </form>

            <h2>📋 Authorized Staff IDs</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Staff ID</th>
                    <th>Date Added</th>
                </tr>
                <?php while ($row = $staff_ids->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['staff_id']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
