<?php
// api/config.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../db.php'; // Adjust path if needed

// JWT secret key (in production, use environment variable)
define('JWT_SECRET', 'your-secret-key-here');

// Function to generate JWT
function generateJWT($payload) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $header_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $payload_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    $signature = hash_hmac('sha256', $header_encoded . "." . $payload_encoded, JWT_SECRET, true);
    $signature_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    return $header_encoded . "." . $payload_encoded . "." . $signature_encoded;
}

// Function to verify JWT
function verifyJWT($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    $signature = hash_hmac('sha256', $parts[0] . "." . $parts[1], JWT_SECRET, true);
    $signature_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    return $signature_encoded === $parts[2];
}

// Function to get user from token
function getUserFromToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) return null;
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    if (!verifyJWT($token)) return null;
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], explode('.', $token)[1])), true);
    return $payload;
}

// Function to check role
function checkRole($required_role) {
    $user = getUserFromToken();
    return $user && $user['role'] === $required_role;
}

// Function to send JSON response
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Function to send error
function sendError($message, $status = 400) {
    sendResponse(['error' => $message], $status);
}
?>