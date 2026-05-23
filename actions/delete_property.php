<?php include '../config/db_connection.php'; ?>

<?php
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$res = mysqli_query($conn, "SELECT image_path FROM property_images WHERE property_id=$id");
while ($row = mysqli_fetch_assoc($res)) {
    $file = __DIR__ . '/../' . $row['image_path'];
    if (file_exists($file)) {
        unlink($file);
    }
}

if ($role == 'admin') {
    mysqli_query($conn, "DELETE FROM properties WHERE id=$id");
} else {
    mysqli_query($conn, "DELETE FROM properties WHERE id=$id AND owner_id=$user_id");
}

$_SESSION['success_msg'] = "Property deleted successfully.";

if ($role == 'admin') {
    header("Location: ../admin_dashboard.php");
} else {
    header("Location: ../owner_dashboard.php");
}
exit();
?>
