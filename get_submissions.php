<?php
// api/get_submissions.php
require_once 'config.php';

$user = getUserFromToken();
if (!$user) {
    sendError('Unauthorized', 401);
}

$role = $user['role'];
$status_filter = '';

if ($role === 'hou') {
    $status_filter = "status = 'pending'";
} elseif ($role === 'hod') {
    $status_filter = "status = 'approved_hou'";
} elseif ($role === 'hr') {
    $status_filter = "status = 'approved_hod'";
} elseif ($role === 'staff') {
    $status_filter = "staff_id = '{$user['staff_id']}'";
} else {
    sendError('Invalid role');
}

$type = $_GET['type'] ?? 'biodata';
$table = $type === 'biodata' ? 'biodata' : 'appraisals';

$query = "SELECT * FROM $table WHERE $status_filter";
$result = $conn->query($query);

$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}

sendResponse(['submissions' => $submissions]);
?>