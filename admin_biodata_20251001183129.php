<?php
session_start();
include("db.php");

// ✅ Ensure only admin can access
if (!isset($_SESSION['admin'])) {
    die("Unauthorized. Admins only.");
}
$query = "SELECT staff_id, surname, firstname, locked, unlock_until FROM staff_biodata";
$result = $conn->query($query);

if (!$result) {
    die("❌ Query Failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Biodata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 250px;
            background: #343a40;
            padding-top: 60px;
            color: #fff;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #ddd;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #495057;
            color: #fff;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .sidebar .logo {
            position: absolute;
            top: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">🛠 Admin Panel</div>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="admin_biodata.php">👨‍💼 Manage Biodata</a>
    <a href="admin_staff.php">🧑‍🤝‍🧑 Staff Records</a>
    <a href="admin_reports.php">📑 Reports</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="mb-4">🔒 Staff Biodata Management</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-info"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Unlock Until</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['staff_id']; ?></td>
                <td><?php echo $row['surname'] . " " . $row['firstname']; ?></td>
                <td>
                    <?php echo $row['locked'] ? "<span class='badge bg-danger'>Locked</span>" : "<span class='badge bg-success'>Unlocked</span>"; ?>
                </td>
                <td><?php echo $row['unlock_until'] ? $row['unlock_until'] : "-"; ?></td>
                <td>
                    <form action="lock_unlock.php" method="POST" class="d-flex">
                        <input type="hidden" name="staff_id" value="<?php echo $row['staff_id']; ?>">

                        <select name="timeframe" class="form-select form-select-sm me-2">
                            <option value="0">Lock Now</option>
                            <option value="1 DAY">1 Day</option>
                            <option value="3 DAY">3 Days</option>
                            <option value="1 WEEK">1 Week</option>
                            <option value="2 WEEK">2 Weeks</option>
                            <option value="1 MONTH">1 Month</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<form method="post" action="unlock_biodata.php">
  <input type="hidden" name="staff_id" value="<?php echo $row['staff_id']; ?>">
  <label>Unlock Until:</label>
  <input type="datetime-local" name="unlock_until" required>
  <button type="submit" class="btn btn-success btn-sm">Unlock</button>
</form>
</body>
</html>