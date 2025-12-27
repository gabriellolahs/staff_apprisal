<?php
// api/admin_dashboard.php
require_once 'config.php';

$user = getUserFromToken();
if (!$user || $user['role'] !== 'hr') {
    sendError('Unauthorized', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

// Get stats
$stats = [];

$result = $conn->query("SELECT COUNT(*) as total_staff FROM users WHERE role = 'staff'");
$stats['total_staff'] = $result->fetch_assoc()['total_staff'];

$result = $conn->query("SELECT COUNT(*) as pending_biodata FROM biodata WHERE status = 'pending'");
$stats['pending_biodata'] = $result->fetch_assoc()['pending_biodata'];

$result = $conn->query("SELECT COUNT(*) as pending_appraisals FROM appraisals WHERE status = 'pending'");
$stats['pending_appraisals'] = $result->fetch_assoc()['pending_appraisals'];

$result = $conn->query("SELECT COUNT(*) as approved_records FROM biodata WHERE status = 'approved_hr'");
$stats['approved_records'] = $result->fetch_assoc()['approved_records'];

sendResponse(['stats' => $stats]);
?>