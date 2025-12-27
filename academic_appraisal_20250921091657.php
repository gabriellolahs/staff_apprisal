<?php
session_start();
include("db.php");

// ✅ Redirect if not logged in
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $performance = $_POST['performance'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO appraisals (staff_id, performance, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $staff_id, $performance, $comment);

    if ($stmt->execute()) {
        echo "<script>alert('Appraisal submitted successfully!'); window.location='academic_appraisal.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Appraisal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .form-box {
            max-width: 600px; margin: auto; background: #fff; padding: 25px;
            border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #2c3e50; }
        textarea, select {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button {
            width: 100%; padding: 12px; background: #2980b9; color: #fff;
            border: none; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #1c5d82; }
        a { display: block; text-align: center; margin-top: 15px; color: #2980b9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Staff Appraisal</h2>
        <form method="post">
            <label>Performance Rating:</label>
            <select name="performance" required>
                <option value="">-- Select --</option>
                <option value="Excellent">Excellent</option>
                <option value="Very Good">Very Good</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
            </select>

            <label>Comment:</label>
            <textarea name="comment" rows="4" placeholder="Enter your comment..." required></textarea>

            <button type="submit">Submit Appraisal</button>
        </form>
        <a href="academic_dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
