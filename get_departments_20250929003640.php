<?php
include "db.php";

if(isset($_POST['school_id'])){
    $school_id = intval($_POST['school_id']);
    $result = $conn->query("SELECT * FROM departments WHERE school_id=$school_id ORDER BY department_name ASC");

    echo "<option value=''>-- Select Department --</option>";
    while($row = $result->fetch_assoc()){
        echo "<option value='".$row['id']."'>".$row['department_name']."</option>";
    }
}
?>
