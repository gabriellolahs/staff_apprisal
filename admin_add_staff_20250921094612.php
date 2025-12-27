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

// Handle add staff
if (isset($_POST['add_staff'])) {
    $staff_id = trim($_POST['staff_id']);

    if (!empty($staff_id)) {
        $check_sql = "SELECT * FROM allowed_staff WHERE staff_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Staff ID already exists!');</script>";
        } else {
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

// Handle delete staff
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM allowed_staff WHERE id = $delete_id");
    echo "<script>alert('Staff deleted successfully!'); window.location='admin_add_staff.php';</script>";
}

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $staff_ids = $conn->query("SELECT * FROM allowed_staff WHERE staff_id LIKE '%$search%' ORDER BY created_at DESC");
} else {
    $staff_ids = $conn->query("SELECT * FROM allowed_staff ORDER BY created_at DESC");
}
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
            background-image: url('20250919_095448.jpg');
            background-repeat: no-repeat;
            background-position:left;
            background-attachment: fixed;
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
            padding: 10px;
            margin: 5px 0;
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
        .btn-delete {
            background: #e74c3c;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .btn-search {
            background: #2980b9;
        }
        .btn-search:hover {
            background: #1f618d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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
        .logo img {
            width: 150px;
            height: auto;
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
        </div>
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">🏠 Dashboard</a>
        <a href="admin_add_staff.php">➕ Add Staff ID</a>
        <a href="manage_staff.php">👥 Manage Staff</a>
        <a href="logout.php">🚪 Logout</a>
        <footer>
            <p>Powered by Thrive's Hub</p>
        </footer>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card">
            <h1>➕ Add Staff ID</h1>
            <form method="POST">
                <input type="text" name="staff_id" placeholder="Enter Staff ID" required>
                <button type="submit" name="add_staff">Add</button>
           
            <h2>🔍 Search Staff</h2>
           
                <input type="text" name="search" placeholder="Enter Staff ID" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">Search</button>
            </form>

            <h2>📋 Authorized Staff IDs</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Staff ID</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $staff_ids->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['staff_id']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="admin_add_staff.php?delete_id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this staff?');">
                           <button type="button" class="btn-delete">Delete</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
