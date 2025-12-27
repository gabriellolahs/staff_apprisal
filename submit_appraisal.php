<?php
// api/submit_appraisal.php
require_once 'config.php';

$user = getUserFromToken();
if (!$user || $user['role'] !== 'staff') {
    sendError('Unauthorized', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$required_fields = ['appraisal_year', 'self_assessment', 'achievements', 'goals', 'training_needs'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        sendError("Missing field: $field");
    }
}

$staff_id = $user['staff_id'];
$appraisal_year = $data['appraisal_year'];
$self_assessment = $data['self_assessment'];
$achievements = $data['achievements'];
$goals = $data['goals'];
$training_needs = $data['training_needs'];

// Check if already submitted for this year
$stmt = $conn->prepare("SELECT id FROM appraisals WHERE staff_id = ? AND appraisal_year = ? AND status != 'rejected_hod'");
$stmt->bind_param("si", $staff_id, $appraisal_year);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendError('Appraisal already submitted for this year');
}

$stmt = $conn->prepare("INSERT INTO appraisals (staff_id, appraisal_year, self_assessment, achievements, goals, training_needs) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissss", $staff_id, $appraisal_year, $self_assessment, $achievements, $goals, $training_needs);

if ($stmt->execute()) {
    // Add notification
    $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) SELECT id, 'New appraisal submission from staff' FROM users WHERE role = 'hou'");
    $stmt2->execute();
    sendResponse(['message' => 'Appraisal submitted successfully']);
} else {
    sendError('Failed to submit appraisal');
}
?>