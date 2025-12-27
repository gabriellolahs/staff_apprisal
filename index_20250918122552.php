<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Appraisal System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #00c6ff, #0072ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logo img {
            width: 150px;      /* adjust size */
            height: auto;      /* keeps proportion */
            margin-bottom: 10px;
            }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.2);
            text-align: center;
            width: 400px;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        a {
            display: block;
            text-decoration: none;
            margin: 10px 0;
            padding: 12px;
            background: #2575fc;
            color: white;
            border-radius: 8px;
            font-size: 16px;
        }
        a:hover {
            background: #1b5edb;
        }
        footer {
    position: absolute;
    bottom: 20px;
    width: 100%;
    text-align: center;
    font-size: 14px;
    color: #00c6ff;
}

footer a {
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    
}

footer a:hover {
    text-decoration: underline;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="System Logo">
        </div>
        <h1>Staff Appraisal</h1>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
     <footer>
        <a href="#">Powered by Thrive's Multiplex Enterprise</a>
    </footer>
</body>
</html>