<?php
session_start();
include("db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_or_username      = isset($_POST['id_or_username']) ? trim($_POST['id_or_username']) : '';
    $surname_or_password = isset($_POST['surname_or_password']) ? trim($_POST['surname_or_password']) : '';

    // 🔹 Check if Admin
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $id_or_username);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult && $adminResult->num_rows === 1) {
        $admin = $adminResult->fetch_assoc();
        if ($surname_or_password === $admin['password']) { // 🔑 Plain password for admin
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid Admin credentials!";
        }
    } else {
        // 🔹 Check if Staff
        $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
        $stmt->bind_param("s", $id_or_username);
        $stmt->execute();
        $staffResult = $stmt->get_result();

        if ($staffResult && $staffResult->num_rows === 1) {
            $staff = $staffResult->fetch_assoc();

            $validLogin = false;

            // Case 1: Staff has not set password → allow Surname
            if (empty($staff['password'])) {
                if (strcasecmp($surname_or_password, $staff['surname']) === 0) {
                    $validLogin = true;
                }
            }
            // Case 2: Staff has already set password → allow only password
            else {
                if (password_verify($surname_or_password, $staff['password'])) {
                    $validLogin = true;
                }
            }

            if ($validLogin) {
                // Save session details
                $_SESSION['staff']     = $staff['staff_id'];
                $_SESSION['surname']   = $staff['surname'];
                $_SESSION['firstname'] = $staff['firstname'];
                $_SESSION['lastname']  = $staff['lastname'];
                $_SESSION['gender']    = $staff['gender'];
                $_SESSION['category']  = $staff['category'];
                $_SESSION['sub_category']  = $staff['sub_category'];
                $_SESSION['passport']  = $staff['passport'];

                // Redirect by category
                if ($staff['category'] == "Teaching Staff") {
                    header("Location: academic_dashboard.php");
                } elseif ($staff['category'] == "Non Teaching Staff") {
                    header("Location: non_academic_dashboard.php");
                } else {
                    $error = "Unknown staff category. Please contact admin.";
                }
                exit();
            } else {
                $error = "Invalid Staff ID or Password!";
            }
        } else {
            $error = " You Have Not Been Authenticate Yet!
            Contact the Admin";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Staff Appraisal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #001f3f, #003087);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .header {
            background: linear-gradient(135deg, #003087, #0056b3);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .logo img {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 2rem;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .body {
            padding: 40px 30px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #003087;
            box-shadow: 0 0 0 0.2rem rgba(0,48,135,0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #003087, #0056b3);
            color: white;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 50px;
            font-weight: bold;
            width: 100%;
            border: none;
            box-shadow: 0 10px 30px rgba(0,48,135,0.4);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #002066, #004494);
            transform: translateY(-3px);
        }
        .alert {
            border-radius: 15px;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            font-size: 0.9rem;
            color: #666;
        }
        .footer a {
            color: #003087;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="header">
        <div class="logo">
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="Logo">
        </div>
        <h2>STAFF LOGIN</h2>
        <p>Federal Polytechnic Ile-Oluji</p>
    </div>

    <div class="body">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Staff ID / Admin Username</label>
                <input type="text" name="id_or_username" class="form-control form-control-lg" 
                       placeholder="Enter Staff ID or Admin Username" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Surname (Staff) / Password (Admin)</label>
                <input type="password" name="surname_or_password" class="form-control form-control-lg" 
                       placeholder="Enter your surname or password" required>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> LOGIN
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="mb-2"><a href="register.php" class="text-primary">Don't have an account? Register New Staff</a>
            <br>
            <small class="text-muted">First time? Use your Surname as password</small>
        </div>
    </div>

    <div class="footer">
        <p>© 2025 Federal Polytechnic Ile-Oluji</p>
        <p>Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1">Thrive's Hub</a></p>
    </div>
</div>
</body>
</html>