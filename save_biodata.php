<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

// ALL FIELDS FROM YOUR FORM — EXACT MATCH!
$fields = [
    'title', 'school', 'department', 'designation', 'marital_status', 'dob', 'pob',
    'nationality', 'state', 'senatorial', 'lga', 'town', 'ward', 'religion', 'hobbies',
    'perm_address', 'contact_address', 'res_address', 'gsm', 'email',
    'blood_group', 'genotype', 'spouse_name', 'children',
    'nok_name', 'nok_relation', 'nok_phone', 'nok_address',
    'first_place_before', 'first_date_before', 'first_place_fpi', 'appt_type',
    'post_appt', 'present_appt', 'regularization', 'gl', 'step',
    'confirmation', 'first_appt_pub', 'accommodation',
    'qualifications', 'union_name', 'specialization', 'pfa', 'pin_code'
];

// Validate required fields
$errors = [];
foreach ($fields as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $errors[] = ucwords(str_replace('_', ' ', $field)) . " is required.";
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: biodata.php");
    exit();
}

// Collect data
$data = [];
foreach ($fields as $field) {
    $data[$field] = trim($_POST[$field] ?? '');
}

// Check if already locked
$stmt = $conn->prepare("SELECT is_locked FROM save_biodata WHERE staff_id = ?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();

if ($existing && $existing['is_locked'] == 1) {
    $_SESSION['message'] = "Your biodata is already submitted and locked!";
    header("Location: biodata.php");
    exit();
}

// Build SQL
$columns = implode(', ', $fields);
$placeholders = str_repeat('?,', count($fields) - 1) . '?';
$update_clause = [];
foreach ($fields as $f) {
    $update_clause[] = "$f = VALUES($f)";
}
$updates = implode(', ', $update_clause);

$sql = $existing
    ? "UPDATE save_biodata SET $updates, is_locked = 1 WHERE staff_id = ?"
    : "INSERT INTO save_biodata (staff_id, $columns, is_locked) VALUES (?, $placeholders, 1)";

$params = $existing ? array_merge(array_values($data), [$staff_id]) : array_merge([$staff_id], array_values($data));
$types = str_repeat('s', count($params));

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error: " . $conn->error);
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['message'] = "Biodata saved and LOCKED successfully!";
} else {
    $_SESSION['message'] = "Error saving biodata.";
}

header("Location: biodata.php");
exit();
?>