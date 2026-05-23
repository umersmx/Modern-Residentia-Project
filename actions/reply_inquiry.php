<?php
include '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$inquiry_id = intval($_POST['inquiry_id']);
$reply = mysqli_real_escape_string($conn, $_POST['reply']);

if ($reply == "") {
    $_SESSION['error_msg'] = "Reply message cannot be empty.";
    header("Location: ../owner_dashboard.php");
    exit();
}

$verify = mysqli_query($conn, "SELECT i.id FROM inquiries i 
          JOIN properties p ON i.property_id = p.id 
          WHERE i.id=$inquiry_id AND p.owner_id=$owner_id");

if (mysqli_num_rows($verify) == 0) {
    $_SESSION['error_msg'] = "Inquiry not found or unauthorized.";
    header("Location: ../owner_dashboard.php");
    exit();
}

$result = mysqli_query($conn, "UPDATE inquiries SET reply='$reply', replied_at=NOW(), is_read=1 WHERE id=$inquiry_id");

if ($result) {
    $_SESSION['success_msg'] = "Reply sent successfully!";
} else {
    $_SESSION['error_msg'] = "Failed to send reply.";
}

header("Location: ../owner_dashboard.php");
exit();
?>
