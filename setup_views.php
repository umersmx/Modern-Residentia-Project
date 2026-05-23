<?php
include 'config/db_connection.php';

$check = mysqli_query($conn, "SHOW COLUMNS FROM properties LIKE 'views'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE properties ADD COLUMN views INT DEFAULT 0 AFTER status");
    echo "✅ Views column added successfully!";
} else {
    echo "✅ Views column already exists.";
}
?>
