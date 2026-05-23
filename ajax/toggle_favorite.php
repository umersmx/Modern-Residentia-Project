<?php
include '../config/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$property_id = intval($_POST['property_id']);

$result = mysqli_query($conn, "SELECT id FROM favorites WHERE user_id=$user_id AND property_id=$property_id");

if (mysqli_num_rows($result) > 0) {
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id=$user_id AND property_id=$property_id");
    echo json_encode(['status' => 'success', 'action' => 'removed', 'message' => 'Removed from favorites']);
} else {
    mysqli_query($conn, "INSERT INTO favorites (user_id, property_id) VALUES ($user_id, $property_id)");
    echo json_encode(['status' => 'success', 'action' => 'added', 'message' => 'Added to favorites']);
}
?>
