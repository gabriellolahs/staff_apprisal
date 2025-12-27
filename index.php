<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Federal Polytechnic Ile-Oluji | Staff Appraisal System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003087;
            --secondary: #0056b3;
            --accent: #f1c40f;
            --light: #f8f9fa;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #001f3f 0%, #003087 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
            backdrop-filter: blur(10px);
        }
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .logo img {
            width: 140px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));
        }
        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            margin: 0;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .body {
            padding: 40px 30px;
            background: white;
            color: #333;
        }
        .btn-custom {
            display: block;
            width: 100%;
            padding: 16px;
            margin: 15px 0;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, var(--secondary), #004494);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,48,135,0.4);
        }
        .btn-register {
            background: white;
            color: var(--primary);
            border: 3px solid var(--primary);
        }
        .btn-register:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-5px);
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.1);
            font-size: 0.9rem;
            color: #ddd;
        }
        .footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: bold;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .header h1 { font-size: 2rem; }
            .body { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="header">
        <div class="logo">
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="Federal Polytechnic Ile-Oluji">
        </div>
        <h1>Staff Appraisal System</h1>
        <p>Performance Evaluation Portal</p>
    </div>

    <div class="body">
        <h3 class="text-center mb-4" style="color: var(--primary);">Welcome Back!</h3>
        <p class="text-center text-muted mb-4">Please login to access your appraisal form</p>

        <a href="login.php" class="btn btn-custom btn-login">
            <i class="fas fa-sign-in-alt"></i> Staff Login
        </a>

        <a href="register.php" class="btn btn-custom btn-register">
            <i class="fas fa-user-plus"></i> Register New Staff
        </a>

     
    </div>

    <div class="footer">
        <p>© 2025 Federal Polytechnic Ile-Oluji</p>
          <div class="text-center mt-4">
            <small class="text-muted">
                <p>Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1" style="color: var(--primary); text-decoration: underline;">
                    Thrive's Hub →
                </a> <br> • All Rights Reserved</p>
            </small> 
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>