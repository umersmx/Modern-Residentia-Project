<?php
include 'config/db_connection.php';

$sql = "CREATE TABLE IF NOT EXISTS property_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    renter_id INT NOT NULL,
    desired_location VARCHAR(255) NOT NULL,
    property_type VARCHAR(100) NOT NULL,
    min_bedrooms INT DEFAULT 1,
    max_budget DECIMAL(10, 2) NOT NULL,
    additional_notes TEXT,
    status ENUM('pending', 'searching', 'found', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (renter_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql)) {
    echo "✅ Table 'property_requests' created successfully or already exists.<br>";
} else {
    echo "❌ Error creating table: " . mysqli_error($conn) . "<br>";
}

echo "<br><a href='renter_dashboard.php'>Go to Renter Dashboard</a>";
?>
