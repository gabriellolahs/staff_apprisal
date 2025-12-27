<?php
session_start();
include("db.php");

if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff'];

$stmt = $conn->prepare("SELECT * FROM appraisals WHERE staff_id = ? ORDER BY id DESC");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Reports</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .report-box {
            max-width: 800px; margin: auto; background: #fff; padding: 25px;
            border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%; border-collapse: collapse; margin-top: 15px;
        }
        th, td {
            padding: 12px; border: 1px solid #ddd; text-align: left;
        }
        th { background: #2980b9; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        a { display: block; text-align: center; margin-top: 15px; color: #2980b9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="report-box">
        <h2>My Appraisal Reports</h2>
        <table>
            <tr>
                <th>Performance</th>
                <th>Comment</th>
                <th>Date Submitted</th>
            </tr>
            <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['performance']; ?></td>
                    <td><?php echo $row['comment']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php } ?>
        </table>
        <a href="academic_dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
