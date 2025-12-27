<?php
// manage_staff.php
session_start();
include("db.php"); 
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "staff_appraisal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Delete staff (if requested)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM staff WHERE id = $id");
    echo "<script>alert('Staff deleted successfully!'); window.location='manage_staff.php';</script>";
}

// ✅ Fetch staff properly
$sql = "SELECT id, staff_id, surname, firstname, lastname, category, passport, created_at 
        FROM staff ORDER BY id DESC";
$staff_list = $conn->query($sql);

if (!$staff_list) {
    die("Query error: " . $conn->error);
}
$passport = "uploads/default.png"; // default image
if (!empty($staff['passport']) && file_exists($staff['passport'])) {
    $passport = $staff['passport']; // already stored with path (e.g., uploads/12345_passport.jpg)
} elseif (!empty($staff['passport']) && file_exists("uploads/" . $staff['passport'])) {
    $passport = "uploads/" . $staff['passport']; // if only filename was saved
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Admin</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; display: flex; background: #f4f4f4; }
        .sidebar { width: 250px; background: #2c3e50; color: #fff; height: 100vh; position: fixed; }
        .sidebar h2 { text-align: center; margin: 20px 0; color: #f1c40f; }
        .sidebar a { display: block; padding: 15px 20px; color: #fff; text-decoration: none; }
        .sidebar a:hover { background: #34495e; }
        .main-content { margin-left: 250px; padding: 20px; flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #2c3e50; color: white; }
        img { border-radius: 50%; }
        .btn { padding: 6px 12px; text-decoration: none; color: #fff; border-radius: 5px; }
        .btn-edit { background: #2980b9; }
        .btn-delete { background: #c0392b; }
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
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>👥 Manage Staff</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Staff ID</th>
                <th>Surname</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Category</th>
                <th>Passport</th>
                <th>Date Registered</th>
                <th>Actions</th>
            </tr>
            <?php if ($staff_list->num_rows > 0): ?>
                <?php while ($row = $staff_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['staff_id']); ?></td>
                    <td><?= htmlspecialchars($row['surname']); ?></td>
                    <td><?= htmlspecialchars($row['firstname']); ?></td>
                    <td><?= htmlspecialchars($row['lastname']); ?></td>
                    <td><?= htmlspecialchars($row['category']); ?></td>
                    <td>
                    <div class="passport">
            <img src="<?php echo htmlspecialchars($passport); ?>" alt="passport">
        </div>
                    </td>
                    <td><?= $row['created_at']; ?></td>
                    <td>
                        <a href="edit_staff.php?id=<?= $row['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="manage_staff.php?delete=<?= $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this staff?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9">No staff found</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
