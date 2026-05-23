<?php
include 'config/db_connection.php';

// Add reply column if it doesn't exist
$check = mysqli_query($conn, "SHOW COLUMNS FROM inquiries LIKE 'reply'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "ALTER TABLE inquiries ADD COLUMN reply TEXT DEFAULT NULL AFTER message");
    mysqli_query($conn, "ALTER TABLE inquiries ADD COLUMN replied_at TIMESTAMP NULL DEFAULT NULL AFTER reply");
    echo "✅ Reply columns added to inquiries table.<br>";
} else {
    echo "✅ Reply columns already exist.<br>";
}

echo "<br><a href='owner_dashboard.php'>Go to Owner Dashboard</a>";
?>
