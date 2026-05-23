<?php
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'];
$message = $_POST['message'];

if ($message == "") {
    $_SESSION['inquiry_error'] = "Please enter a message.";
    header("Location: ../property_detail.php?id=$property_id");
    exit();
}

$result = mysqli_query($conn, "SELECT id FROM properties WHERE id=$property_id");
if (mysqli_num_rows($result) == 0) {
    $_SESSION['inquiry_error'] = "Invalid property.";
    header("Location: ../properties.php");
    exit();
}

$safe_message = mysqli_real_escape_string($conn, $message);

if (mysqli_query($conn, "INSERT INTO inquiries (sender_id, property_id, message) VALUES ($sender_id, $property_id, '$safe_message')")) {
    $_SESSION['inquiry_success'] = true;
} else {
    $_SESSION['inquiry_error'] = "Error sending inquiry. Please try again.";
}

header("Location: ../property_detail.php?id=$property_id");
exit();
?>
