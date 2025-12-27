<?php
session_start();
require_once "db.php";

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['staff_id'];
    $action = $_POST['action'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

   if ($action === 'approve') {
    $clean_comment = strip_tags($comment); // Remove any HTML
    $approval_text = "APPROVED BY: $logged_in_admin\nDate: " . date('d F Y, h:i A');
    $final_comment = $clean_comment . "\n\n" . $approval_text;
    $stmt = $conn->prepare("UPDATE staff_appraisal SET admin_approved = 1, admin_comment = ? WHERE staff_id = ?");
    $stmt->bind_param("ss", $final_comment, $staff_id);
    $stmt->execute();
}

if ($action === 'reject') {
    $clean_comment = strip_tags($comment);
    $reject_text = "REJECTED - RETURNED FOR CORRECTION BY: $logged_in_admin\nDate: " . date('d F Y, h:i A');
    $final_comment = $clean_comment . "\n\n" . $reject_text;
    $stmt = $conn->prepare("UPDATE staff_appraisal SET is_locked = 0, admin_approved = 0, admin_comment = ? WHERE staff_id = ?");
    $stmt->bind_param("ss", $final_comment, $staff_id);
    $stmt->execute();
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin - Review Appraisals</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f0f2f5; padding: 20px; }
    .card { box-shadow: 0 10px 30px rgba(0,0,0,0.2); border-radius: 15px; }
    .header { background: #003087; color: white; padding: 25px; text-align: center; border-radius: 15px 15px 0 0; }
    .photo { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
    .btn-approve { background: #28a745; color: white; width: 60px; height: 60px; border-radius: 50%; }
    .btn-reject { background: #dc3545; color: white; width: 60px; height: 60px; border-radius: 50%; }
    .btn-unlock { background: #fd7e14; color: white; }
    .comment-box { min-height: 100px; resize: vertical; }
    .status { font-weight: bold; padding: 8px 16px; border-radius: 25px; }
</style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>ADMIN APPRAISAL REVIEW</h1>
            <p>Logged in as: <strong><?= $logged_in_admin ?></strong></p>
        </div>

        <div class="card-body p-4">
            <a href="admin_dashboard.php" class="btn btn-light mb-3">← Back</a>

            <?php
            $result = $conn->query("SELECT DISTINCT s.staff_id, s.surname, s.firstname, s.lastname, s.passport,
                sa.is_locked, sa.admin_approved, sa.admin_comment, sa.staff_response, sa.submitted_at
                FROM staff s LEFT JOIN staff_appraisal sa ON s.staff_id = sa.staff_id 
                WHERE sa.staff_id IS NOT NULL ORDER BY sa.submitted_at DESC");

            if ($result && mysqli_num_rows($result) > 0):
                while ($r = mysqli_fetch_assoc($result)): 
                    $name = $r['surname'].' '.$r['firstname'];
                    $photo = $r['passport'] && file_exists($r['passport']) ? $r['passport'] : 'uploads/default.png';
                    $locked = $r['is_locked'];
                    $approved = $r['admin_approved'];
            ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex">
                        <img src="<?= $photo ?>" class="photo rounded me-3">
                        <div>
                            <strong><?= $r['staff_id'] ?> - <?= $name ?></strong>
                            <br><small class="text-light">Submitted: <?= date('d M Y, h:i A', strtotime($r['submitted_at'])) ?></small>
                        </div>
                        <span class="ms-auto badge status <?= $approved ? 'bg-success' : ($locked ? 'bg-warning' : 'bg-danger') ?>">
                            <?= $approved ? 'APPROVED' : ($locked ? 'PENDING' : 'SENT BACK') ?>
                        </span>
                         <a href="admin_view_appraisal.php" class="btn btn-light mb-3">View Appraisals</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($r['staff_response']): ?>
                        <div class="alert alert-info">
                            <strong>Staff Response:</strong> <?= nl2br(htmlspecialchars($r['staff_response'])) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="staff_id" value="<?= $r['staff_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Comment:</label>
                            <textarea name="comment" class="form-control comment-box" placeholder="Enter your review, approval, or rejection comment..." required><?= htmlspecialchars($r['admin_comment'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <?php if (!$approved && $locked): ?>
                                <button type="submit" name="action" value="approve" class="btn btn-approve" title="Approve">
                                    <i class="fas fa-check fa-2x"></i><br><small>APPROVE</small>
                                </button>
                                <button type="submit" name="action" value="reject" class="btn btn-reject" title="Reject & Unlock">
                                    <i class="fas fa-times fa-2x"></i><br><small>REJECT</small>
                                </button>
                            <?php endif; ?>
                            <?php if ($locked): ?>
                                <button type="submit" name="action" value="unlock" class="btn btn-warning" title="Unlock for editing">
                                    <i class="fas fa-unlock fa-2x"></i><br><small>UNLOCK</small>
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if ($r['admin_comment']): ?>
                        <hr>
                        <div class="bg-light p-3 rounded mt-3">
                            <strong>Previous Admin Comment:</strong><br>
                            <?= nl2br(htmlspecialchars($r['admin_comment'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h4>No appraisals submitted yet</h4>
                <p class="text-muted">Staff will appear here once they submit their appraisal forms.</p>
            </div>
        <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>