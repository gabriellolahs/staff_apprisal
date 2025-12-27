<?php
session_start();
include("db.php");

// Ensure staff is logged in
if (!isset($_SESSION['staff'])) {
    die("Unauthorized access. Please login.");
}

$staff_id = $_SESSION['staff'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely read form values
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass     = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    // Fetch staff record
    $stmt = $conn->prepare("SELECT surname, password FROM staff WHERE staff_id = ?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $db_password = $row['password']; // hashed password (if already set)
        $db_surname  = $row['surname'];  // surname (if first login)

        $validCurrent = false;

        // Case 1: first time (no password set yet)
        if (empty($db_password)) {
            if (strcasecmp($current_pass, $db_surname) === 0) {
                $validCurrent = true;
            }
        } else {
            // Case 2: normal login (verify hashed password)
            if (password_verify($current_pass, $db_password)) {
                $validCurrent = true;
            }
        }

        if ($validCurrent) {
            if ($new_pass === $confirm_pass) {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

                $update = $conn->prepare("UPDATE staff SET password=? WHERE staff_id=?");
                $update->bind_param("ss", $hashed, $staff_id);

                if ($update->execute()) {
                    $message = "<p style='color:green;'>✅ Password updated successfully!</p>";
                } else {
                    $message = "<p style='color:red;'>❌ Error updating password. Please try again.</p>";
                }
            } else {
                $message = "<p style='color:red;'>⚠️ New password and confirmation do not match!</p>";
            }
        } else {
            $message = "<p style='color:red;'>❌ Current password is incorrect!</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; }
        .container {
            width: 400px; margin: 50px auto; background: #fff; padding: 20px;
            border-radius: 8px; box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; }
        input {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button {
            width: 100%; padding: 10px; background: #2980b9; color: #fff;
            border: none; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #1c5d82; }
        .msg { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Set Your Password</h2>
        <div class="msg"><?php echo $message; ?></div>
        <form method="POST">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Save Password</button>
                <a href="non_academic_dashboard.php" class="btn-back">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
