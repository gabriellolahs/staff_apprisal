<?php
// api/get_notifications.php
require_once 'config.php';

$user = getUserFromToken();
if (!$user) {
    sendError('Unauthorized', 401);
}

$user_id = $user['id'];

$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

sendResponse(['notifications' => $notifications]);
?>