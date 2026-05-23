<?php include '../config/db_connection.php'; ?>

<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_GET['id'];
$status = $_GET['status'];

$allowed = array('approved', 'rejected', 'rented', 'pending');
if (in_array($status, $allowed)) {
    mysqli_query($conn, "UPDATE properties SET status='$status' WHERE id=$id");
}

header("Location: ../admin_dashboard.php");
exit();
?>
