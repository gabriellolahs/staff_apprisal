
<?php
session_start();
require_once "db.php";

// === ADMIN CHECK – WORKS WITH YOUR CURRENT SYSTEM ===
$admin_users = ['STF001', 'STF002', 'ADMIN001']; // ← Put your admin Staff IDs here

if (!isset($_SESSION['staff']) || !in_array($_SESSION['staff'], $admin_users)) {
    die("<h3 style='color:red;text-align:center;margin-top:50px;'>
            Access Denied!<br><br>
            <a href='login.php' class='btn btn-primary'>Login as Admin</a>
         </h3>");
}
// ====================================================

$logged_in_admin = $_SESSION['staff']; // for display

// Handle actions
if (isset($_GET['action']) && isset($_GET['staff_id'])) {
    $staff_id = $_GET['staff_id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE staff_appraisal SET admin_approved = 1, admin_comment = 'Approved by Admin' WHERE staff_id = '$staff_id'");
        $msg = "Appraisal APPROVED for $staff_id";
    }
    if ($action === 'reject') {
        $comment = $conn->real_escape_string($_POST['comment'] ?? 'Please review and resubmit.');
        $conn->query("UPDATE staff_appraisal SET admin_approved = 0, admin_comment = '$comment' WHERE staff_id = '$staff_id'");
        $msg = "Appraisal sent back for review";
    }
    if ($action === 'unlock') {
        $conn->query("UPDATE staff_appraisal SET is_locked = 0, admin_approved = 0, admin_comment = 'Unlocked by Admin for editing' WHERE staff_id = '$staff_id'");
        $msg = "Appraisal UNLOCKED — staff can now edit";
    }
}

// Search
$search = $_GET['search'] ?? '';
$where = $search ? "WHERE s.staff_id LIKE '%$search%' OR s.surname LIKE '%$search%' OR s.firstname LIKE '%$search%'" : "";

// Fetch all staff with appraisal status
$sql = "SELECT s.staff_id, s.surname, s.firstname, s.lastname, s.passport,
               sa.is_locked, sa.admin_approved, sa.admin_comment, sa.submitted_at
        FROM staff s
        LEFT JOIN staff_appraisal sa ON s.staff_id = sa.staff_id
        $where
        GROUP BY s.staff_id
        ORDER BY sa.submitted_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Appraisal Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #001f3f, #003087);
            min-height: 100vh;
            padding: 20px 0;
        }
        .container { max-width: 1200px; }
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: #003087;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5rem;
            margin: 0;
        }
        .table {
            margin: 0;
        }
        .badge-locked { background: #dc3545; }
        .badge-approved { background: #28a745; }
        .badge-pending { background: #ffc107; color: black; }
        .btn-unlock { background: #fd7e14; }
        .btn-approve { background: #28a745; }
        .btn-reject { background: #dc3545; }
        .photo-small {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #003087;
        }
        .search-box {
            max-width: 500px;
            margin: 20px auto;
        }
        .alert {
            border-radius: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1><i class="fas fa-user-shield"></i> ADMIN APPRAISAL PANEL</h1>
            <p>Federal Polytechnic Ile-Oluji • HR Department</p>
        </div>

        <div class="p-4">

            <?php if (isset($msg)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <form method="get" class="search-box">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Search Staff ID or Name..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary btn-lg"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Photo</th>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Comment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): 
                            $name = trim($row['surname'] . ' ' . $row['firstname'] . ' ' . $row['lastname']);
                            $photo = $row['passport'] && file_exists($row['passport']) ? $row['passport'] : 'uploads/default.png';
                            $locked = $row['is_locked'] == 1;
                            $approved = $row['admin_approved'] == 1;
                        ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($photo) ?>" class="photo-small" alt="Photo"></td>
                            <td><strong><?= htmlspecialchars($row['staff_id']) ?></strong></td>
                            <td><?= htmlspecialchars($name) ?></td>
                            <td><?= $row['submitted_at'] ? date('d M Y', strtotime($row['submitted_at'])) : 'Not submitted' ?></td>
                            <td>
                                <?php if (!$locked): ?>
                                    <span class="badge bg-secondary">Not Submitted</span>
                                <?php elseif ($approved): ?>
                                    <span class="badge badge-approved">APPROVED</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">PENDING</span>
                                <?php endif; ?>
                                <?php if ($locked): ?>
                                    <span class="badge badge-locked ms-1">LOCKED</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['admin_comment'] ?? '-') ?></td>
                            <td>
                                <?php if ($locked && !$approved): ?>
                                    <a href="?action=approve&staff_id=<?= $row['staff_id'] ?>" class="btn btn-approve btn-sm">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <button type="button" class="btn btn-reject btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?= $row['staff_id'] ?>">
                                        <i class="fas fa-times"></i> Send Back
                                    </button>
                                <?php endif; ?>
                                <?php if ($locked): ?>
                                    <a href="?action=unlock&staff_id=<?= $row['staff_id'] ?>" class="btn btn-unlock btn-sm text-white" onclick="return confirm('Unlock appraisal for editing?')">
                                        <i class="fas fa-unlock"></i> Unlock
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal">
    <div class="modal-dialog">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Back for Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="staff_id" id="rejectStaffId">
                    <textarea name="comment" class="form-control" rows="4" placeholder="Enter reason or comment..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="reject" class="btn btn-danger">Send Back</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('[data-bs-target="#rejectModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('rejectStaffId').value = this.dataset.id;
    });
});
</script>
</body>
</html>