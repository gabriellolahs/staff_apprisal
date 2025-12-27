<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id   = trim($_POST['staff_id']);
    $surname    = trim($_POST['surname']);
    $firstname  = trim($_POST['firstname']);
    $lastname   = trim($_POST['lastname']);
    $gender     = trim($_POST['gender']);
    $category   = trim($_POST['category']);
    $sub_category = trim($_POST['sub_category'] ?? '');

$error = '';
// ✅ Handle passport upload
// PASSPORT UPLOAD — CLEAN & PERFECT
$target_dir = "uploads/passports/";  // Create this folder in your project
$default_photo = "uploads/default.png";

// Create folder if not exist
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$passport_path = $default_photo; // Default if upload fails

if (isset($_FILES['passport']) && $_FILES['passport']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['passport'];
    $file_name = $file['name'];
    $file_tmp  = $file['tmp_name'];
    $file_size = $file['size'];

    // Get file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validate file type
    $allowed = ['jpg', 'jpeg', 'png'];
    if (!in_array($file_ext, $allowed)) {
        $error = "Invalid file type! Only JPG, JPEG, PNG allowed.";
    } 
    // Validate file size (max 5MB)
    elseif ($file_size > 5 * 1024 * 1024) {
        $error = "File too large! Max 5MB allowed.";
    } 
    else {
        // Create unique, clean filename
        $new_filename = $staff_id . "_" . time() . "." . $file_ext;
        $destination = $target_dir . $new_filename;

        // Move file
        if (move_uploaded_file($file_tmp, $destination)) {
            $passport_path = $destination; // Save full path to DB
        } else {
            $error = "Failed to upload photo. Please try again.";
        }
    }
} else {
    // No file uploaded or error
    $error = "Please upload a passport photo.";
}

    // 🔹 Insert into database
    $sql = "INSERT INTO staff (staff_id, surname, firstname, lastname, gender, category, sub_category, passport) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("SQL ERROR: " . $conn->error);
    }

    $stmt->bind_param("ssssssss",
                $staff_id, $surname, $firstname, $lastname, $gender,
                $category, $sub_category, $passport_path
            );

    if ($stmt->execute()) {
        header("Location: login.php?success=1");
        exit;
    } else {
        echo "Database Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #001f3f, #003087);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
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
            margin-bottom: 15px;
        }
        .header h2 {
            font-size: 2rem;
            margin: 0;
            font-weight: bold;
        }
        .body {
            padding: 40px 30px;
        }
        .form-label {
            font-weight: bold;
            color: #003087;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #ddd;
        }
        .form-control:focus, .form-select:focus {
            border-color: #003087;
            box-shadow: 0 0 0 0.2rem rgba(0,48,135,0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #003087, #0056b3);
            color: white;
            padding: 15px;
            font-size: 1.3rem;
            border-radius: 50px;
            font-weight: bold;
            width: 100%;
            border: none;
            box-shadow: 0 10px 30px rgba(0,48,135,0.4);
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #002066, #004494);
            transform: translateY(-3px);
        }
        .footer {
            text-align: center;
            background: #f8f9fa;
            font-size: 0.9rem;
            color: #666;
        }
        .footer a {
            color: #003087;
            text-decoration: none;
            font-weight: bold;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="header">
        <div class="logo">
            <img src="20250406_220923_transcpr-removebg-preview.png" alt="Logo">
        </div>
        <h2>Staff Registration</h2>
        <p>Federal Polytechnic Ile-Oluji</p>
    </div>

    <div class="body">
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Staff ID</label>
                <input type="text" name="staff_id" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Surname</label>
                    <input type="text" name="surname" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstname" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastname" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category" id="category" class="form-select" required onchange="toggleSubCategory()">
                    <option value="">-- Select Category --</option>
                    <option value="Teaching Staff">Teaching Staff</option>
                    <option value="Non Teaching Staff">Non Teaching Staff</option>
                </select>
            </div>

            <!-- SUB-CATEGORY (ONLY FOR NON-TEACHING) -->
            <div class="mb-3" id="subCategoryDiv" style="display:none;">
                <label class="form-label">Staff Level</label>
                <select name="sub_category" class="form-select">
                    <option value="">-- Select Level --</option>
                    <option value="Junior Staff">Junior Staff</option>
                    <option value="Senior Staff">Senior Staff</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Passport Photo</label>
                <input type="file" name="passport" class="form-control" accept="image/*" required>
                <small class="text-muted">Upload clear passport photograph</small>
            </div>

            <button type="submit" class="btn btn-register">
                <i class="fas fa-user-plus"></i> REGISTER STAFF
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="login.php" class="text-primary">
                Already have an account? Login here →
            </a>
        </div>
    </div>

    <div class="footer">
        <p>© 2025 Federal Polytechnic Ile-Oluji • All Rights Reserved</p>
        <p>Powered by <a href="https://wa.me/qr/3MCBSQUMTBQRJ1">Thrive's Hub</a></p>
    </div>
</div>

<script>
// Show/hide sub-category dropdown
function toggleSubCategory() {
    const category = document.getElementById('category').value;
    const subDiv = document.getElementById('subCategoryDiv');
    
    if (category === 'Non Teaching Staff') {
        subDiv.style.display = 'block';
    } else {
        subDiv.style.display = 'none';
        // Clear selection when hidden
        subDiv.querySelector('select').value = '';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>