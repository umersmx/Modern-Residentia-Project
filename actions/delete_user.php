<?php include '../config/db_connection.php'; ?>

<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_GET['id'];

if ($id != $_SESSION['user_id']) {
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
}

header("Location: ../admin_dashboard.php");
exit();
?>
