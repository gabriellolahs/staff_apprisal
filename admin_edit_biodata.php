<?php
session_start();
require_once "db.php";

// ADD SCHOOL
if (isset($_POST['add_school'])) {
    $school = trim($_POST['school_name']);
    if (!empty($school)) {
        $stmt = $conn->prepare("INSERT INTO schools (school_name) VALUES (?)");
        $stmt->bind_param("s", $school);
        $stmt->execute();
        echo "<script>alert('School added successfully!');</script>";
    }
}

// ADD DEPARTMENT
if (isset($_POST['add_dept'])) {
    $dept = trim($_POST['dept_name']);
    $school_id = $_POST['school_id'];
    if (!empty($dept) && !empty($school_id)) {
        $stmt = $conn->prepare("INSERT INTO departments (department_name, school_id) VALUES (?, ?)");
        $stmt->bind_param("si", $dept, $school_id);
        $stmt->execute();
        echo "<script>alert('Department added successfully!');</script>";
    }
}

// DELETE SCHOOL
if (isset($_GET['delete_school'])) {
    $id = (int)$_GET['delete_school'];
    $conn->query("DELETE FROM schools WHERE id = $id");
    echo "<script>alert('School deleted!'); location='admin_edit_biodata.php';</script>";
}

// DELETE DEPARTMENT
if (isset($_GET['delete_dept'])) {
    $id = (int)$_GET['delete_dept'];
    $conn->query("DELETE FROM departments WHERE id = $id");
    echo "<script>alert('Department deleted!'); location='admin_edit_biodata.php';</script>";
}

// Fetch all
$schools = $conn->query("SELECT * FROM schools ORDER BY school_name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Schools & Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #001f3f, #003087); color: white; padding: 30px; }
        .container { max-width: 1100px; background: white; color: #333; border-radius: 20px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
        .header { background: #003087; color: white; padding: 30px; text-align: center; border-radius: 15px; margin: -40px -40px 30px; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .btn-add { background: #28a745; color: white; }
        .btn-delete { background: #dc3545; color: white; }
        .table th { background: #003087; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>ADMIN PANEL</h1>
        <h3>Manage Schools & Departments</h3>
    </div>

    <div class="row">
        <!-- ADD SCHOOL -->
        <div class="col-md-6">
            <div class="card p-4 mb-4">
                <h4 class="text-primary"><i class="fas fa-university"></i> Add New School</h4>
                <form method="post">
                    <div class="input-group">
                        <input type="text" name="school_name" class="form-control" placeholder="Enter school name" required>
                        <button type="submit" name="add_school" class="btn btn-add">Add School</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ADD DEPARTMENT -->
        <div class="col-md-6">
            <div class="card p-4 mb-4">
                <h4 class="text-success"><i class="fas fa-building"></i> Add New Department</h4>
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <input type="text" name="dept_name" class="form-control" placeholder="Department name" required>
                        </div>
                        <div class="col-md-3">
                            <select name="school_id" class="form-select" required>
                                <option value="">Select School</option>
                                <?php foreach ($schools as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['school_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_dept" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- LIST ALL SCHOOLS & DEPARTMENTS -->
    <div class="card p-4">
        <h4 class="text-center mb-4">Current Schools & Departments</h4>
        <?php foreach ($schools as $school): 
            $depts = $conn->query("SELECT * FROM departments WHERE school_id = {$school['id']}")->fetch_all(MYSQLI_ASSOC);
        ?>
            <div class="card mb-3 border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <strong><?= htmlspecialchars($school['school_name']) ?></strong>
                    <a href="?delete_school=<?= $school['id'] ?>" 
                       onclick="return confirm('Delete this school and all its departments?')" 
                       class="btn btn-danger btn-sm">Delete School</a>
                </div>
                <div class="card-body">
                    <?php if (empty($depts)): ?>
                        <p class="text-muted">No departments yet.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($depts as $d): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <?= htmlspecialchars($d['department_name']) ?>
                                    <a href="?delete_dept=<?= $d['id'] ?>" 
                                       onclick="return confirm('Delete this department?')" 
                                       class="btn btn-outline-danger btn-sm">Delete</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <a href="admin_dashboard.php" class="btn btn-light btn-lg">Back to Dashboard</a>
    </div>
</div>

</body>
</html>