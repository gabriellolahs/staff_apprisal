<?php
// setup.php
require_once 'db.php';

$sql = file_get_contents('schema.sql');

if ($conn->multi_query($sql)) {
    echo "Database schema created successfully.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>