<?php
include 'config/db_connection.php';

$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'avatar'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL DEFAULT NULL AFTER phone");
    echo "✅ Avatar column added successfully!";
} else {
    echo "✅ Avatar column already exists.";
}
?>
