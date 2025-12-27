<?php
// api/review_hod.php
require_once 'config.php';

$user = getUserFromToken();
if (!$user || $user['role'] !== 'hod') {
    sendError('Unauthorized', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$submission_id = $data['submission_id'] ?? '';
$type = $data['type'] ?? ''; // 'biodata' or 'appraisal'
$action = $data['action'] ?? ''; // 'approve' or 'reject'
$comment = $data['comment'] ?? '';

if (!$submission_id || !$type || !$action) {
    sendError('Missing required fields');
}

$table = $type === 'biodata' ? 'biodata' : 'appraisals';
$status = $action === 'approve' ? 'approved_hr' : 'rejected_hod';

$stmt = $conn->prepare("UPDATE $table SET status = ?, hod_comment = ? WHERE id = ? AND status = 'approved_hou'");
$stmt->bind_param("ssi", $status, $comment, $submission_id);

if ($stmt->execute()) {
    if ($action === 'approve') {
        // Notify HR
        $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) SELECT id, 'Submission approved by HOD, ready for HR review' FROM users WHERE role = 'hr'");
        $stmt2->execute();
    } else {
        // Notify staff
        $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) SELECT u.id, 'Your $type submission was rejected by HOD: $comment' FROM users u JOIN $table t ON u.staff_id = t.staff_id WHERE t.id = ?");
        $stmt2->bind_param("i", $submission_id);
        $stmt2->execute();
    }
    sendResponse(['message' => 'Review submitted']);
} else {
    sendError('Failed to update');
}
?>