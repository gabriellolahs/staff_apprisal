<?php
session_start();
require_once "db.php";

// Unlock biodata
if (isset($_POST['unlock'])) {
    $staff_id = $_POST['staff_id'];
    $unlock_time = $_POST['unlock_time'];
    $unlocked_by = $_SESSION['staff'];
    
    $stmt = $conn->prepare("UPDATE save_biodata SET is_locked = 0, unlock_time = ?, unlocked_by = ? WHERE staff_id = ?");
    $stmt->bind_param("sss", $unlock_time, $unlocked_by, $staff_id);
    $stmt->execute();
    
    echo "<script>alert('Biodata unlocked for $staff_id until $unlock_time'); location.reload();</script>";
}

// Fetch all staff with biodata status
$staffs = $conn->query("
    SELECT s.*, b.is_locked, b.unlock_time, b.unlocked_by 
    FROM staff s 
    LEFT JOIN save_biodata b ON s.staff_id = b.staff_id 
    ORDER BY s.staff_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #001f3f; color: white; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 1200px; margin: 50px auto; }
        .header { background: #003087; padding: 30px; text-align: center; border-radius: 15px; }
        .card { background: rgba(255,255,255,0.1); border: none; border-radius: 15px; }
        .table { background: white; color: #333; border-radius: 15px; overflow: hidden; }
        .btn-unlock { background: #fd7e14; color: white; }
        .btn-unlock:hover { background: #e66a0d; }
        .locked { background: #dc3545; color: white; }
        .unlocked { background: #28a745; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>SUPER ADMIN PANEL</h1>
        <p>Federal Polytechnic Ile-Oluji - Staff Biodata Management</p>
        <a href="admin_dashboard.php" class="btn btn-light">← Back to Dashboard</a>
    </div>

    <div class="card mt-4 p-4">
<h3 class="text-white fw-bold">All Staff Biodata Status</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Unlock Until</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffs as $s): 
                        $locked = $s['is_locked'];
                        $unlock_time = $s['unlock_time'] ? date('d M Y, h:i A', strtotime($s['unlock_time'])) : '-';
                    ?>
                    <tr>
                        <td><strong><?= $s['staff_id'] ?></strong></td>
                        <td><?= $s['surname'] . ' ' . $s['firstname'] ?></td>
                        <td><?= $s['category'] ?></td>
                        <td>
                            <span class="badge <?= $locked ? 'locked' : 'unlocked' ?>">
                                <?= $locked ? 'LOCKED' : 'UNLOCKED' ?>
                            </span>
                        </td>
                        <td><?= $unlock_time ?></td>
                        <td>
                            <?php if ($locked): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="staff_id" value="<?= $s['staff_id'] ?>">
                                <input type="datetime-local" name="unlock_time" required class="form-control form-control-sm d-inline-block w-auto">
                                <button type="submit" name="unlock" class="btn btn-unlock btn-sm">Unlock</button>
                            </form>
                            <?php else: ?>
                                <span class="text-success">Unlocked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>