<?php
// api/submit_biodata.php
require_once 'config.php';

$user = getUserFromToken();
if (!$user || $user['role'] !== 'staff') {
    sendError('Unauthorized', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$required_fields = ['full_name', 'date_of_birth', 'gender', 'address', 'phone', 'email', 'qualifications', 'experience'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        sendError("Missing field: $field");
    }
}

$staff_id = $user['staff_id'];
$full_name = $data['full_name'];
$date_of_birth = $data['date_of_birth'];
$gender = $data['gender'];
$address = $data['address'];
$phone = $data['phone'];
$email = $data['email'];
$qualifications = $data['qualifications'];
$experience = $data['experience'];

// Check if already submitted
$stmt = $conn->prepare("SELECT id FROM biodata WHERE staff_id = ? AND status != 'rejected_hod'");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendError('Biodata already submitted');
}

$stmt = $conn->prepare("INSERT INTO biodata (staff_id, full_name, date_of_birth, gender, address, phone, email, qualifications, experience) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $staff_id, $full_name, $date_of_birth, $gender, $address, $phone, $email, $qualifications, $experience);

if ($stmt->execute()) {
    // Add notification
    $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, message) SELECT id, 'New biodata submission from staff' FROM users WHERE role = 'hou'");
    $stmt2->execute();
    sendResponse(['message' => 'Biodata submitted successfully']);
} else {
    sendError('Failed to submit biodata');
}
?>