<?php
// api/login.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    sendError('Username and password required');
}

$stmt = $conn->prepare("SELECT id, username, password, role, staff_id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendError('Invalid credentials', 401);
}

$user = $result->fetch_assoc();
if (!password_verify($password, $user['password'])) {
    sendError('Invalid credentials', 401);
}

$payload = [
    'id' => $user['id'],
    'username' => $user['username'],
    'role' => $user['role'],
    'staff_id' => $user['staff_id']
];

$token = generateJWT($payload);

sendResponse(['token' => $token, 'user' => $payload]);
?>