<?php
session_start();
include("db.php");

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_pass = trim($_POST['old_pass']);
    $new_pass = trim($_POST['new_pass']);
    $confirm_pass = trim($_POST['confirm_pass']);

    if ($new_pass !== $confirm_pass) {
        $message = "❌ New password and confirm password do not match!";
    } else {
        $staff_id = $_SESSION['staff'];

        // Get current password
        $stmt = $conn->prepare("SELECT password, surname FROM staff WHERE staff_id = ?");
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $db_pass = $row['password'];
        $surname = $row['surname'];

        $isValidOld = false;

        if (empty($db_pass)) {
            // First login → old password is surname
            if (strcasecmp($old_pass, $surname) === 0) {
                $isValidOld = true;
            }
        } else {
            // Normal login → verify old password
            if (password_verify($old_pass, $db_pass)) {
                $isValidOld = true;
            }
        }

        if ($isValidOld) {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE staff SET password=? WHERE staff_id=?");
            $update->bind_param("ss", $hashed_pass, $staff_id);

            if ($update->execute()) {
                $message = "✅ Password updated successfully!";
            } else {
                $message = "❌ Error updating password!";
            }
        } else {
            $message = "❌ Old password is incorrect!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; display:flex; justify-content:center; align-items:center; height:100vh; }
        .box { background:#fff; padding:25px; border-radius:8px; box-shadow:0 3px 6px rgba(0,0,0,0.2); width:320px; }
        input { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#2980b9; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        button:hover { background:#1c5d82; }
        .msg { margin:10px 0; color:red; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Change Password</h2>
        <?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>
        <form method="POST">
            <input type="password" name="old_pass" placeholder="Old Password" required>
            <input type="password" name="new_pass" placeholder="New Password" required>
            <input type="password" name="confirm_pass" placeholder="Confirm New Password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
