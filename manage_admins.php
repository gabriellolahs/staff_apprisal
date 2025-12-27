<?php
session_start();
include("db.php");

// Protect page
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// ADD NEW ADMIN
if (isset($_POST['add_admin'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    if ($stmt->execute()) {
        $msg = "New admin added successfully!";
    } else {
        $error = "Error: Could not add admin (username may already exist).";
    }
}

// DELETE ADMIN
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM admins WHERE id = $id");
    header("Location: manage_admins.php");
    exit();
}

// GET ALL ADMINS
$result = $conn->query("SELECT * FROM admins ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <style>
        body {
            margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    min-height: 50vh; /* Ensures the body takes at least the full viewport height */
    background-image: url('20250919_095448.jpg'); /* Specifies the background image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
    background-position:left; /* Centers the image horizontally and vertically */
    background-attachment: fixed; /* Keeps the background fixed during scroll */
    background-size: cover;
        }
        .container {
            margin-left: 220px; /* for sidebar space */
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        .msg { color: green; }
        .error { color: red; }
        form {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
        }
        input {
            padding: 10px;
            margin: 5px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 15px;
            border: none;
            background: #2980b9;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1c5d82;
        }
        table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #2980b9;
            color: white;
        }
        a.delete {
            color: red;
            text-decoration: none;
        }
        a.delete:hover {
            text-decoration: underline;
        }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            color: #fff;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #f1c40f;
        }
        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        footer {
            margin-top: 200px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }
        .logo img {
            width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
         <div class="logo">
            <center>
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="System Logo">
    </center>
        </div>
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">🏠 Dashboard</a>
        <a href="admin_add_staff.php">➕ Add Staff ID</a>
        <a href="manage_staff.php">👥 Manage Staff</a>
        <a href="logout.php">🚪 Logout</a>
         <footer>
    <p>Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1">Thrive's Hub</a></p>
</footer>
    </div>

    <!-- Menu bar is already in admin_dashboard.php -->
    <div class="container">
        <h2>Manage Admins</h2>
        <?php if (isset($msg)) echo "<p class='msg'>$msg</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <!-- Add Admin Form -->
        <form method="POST">
            <h3>Add New Admin</h3>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_admin">Add Admin</button>
        </form>

        <!-- Admin List -->
        <h3>Existing Admins</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <a class="delete" href="manage_admins.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
         
    </div>
</body>
</html>
