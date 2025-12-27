<?php
session_start();
include("db.php");

// ✅ Ensure only admin can access
if (!isset($_SESSION['admin'])) {
    die("Unauthorized. Admins only.");
}

// ✅ Fetch all staff biodata
$result = $conn->query("SELECT staff_id, surname, first_name, locked, unlock_until FROM staff_biodata");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Biodata</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
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
                <td><?php echo $row['surname'] . " " . $row['first_name']; ?></td>
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

</body>
</html>
