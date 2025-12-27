<?php
session_start();

// Hardcoded admin credentials
$admin_username = "FedpolelAdmin";
// Hashed version of "FedpolelAdmin123"
$admin_password_hash = password_hash("FedpolelAdmin123", PASSWORD_DEFAULT);

// If already logged in, go to dashboard
if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Check login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Hardcoded admin login (for now)
    if ($username === "FedpolelAdmin" && $password === "FedpolelAdmin123") {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #2980b9;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1c5d82;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
