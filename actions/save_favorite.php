<?php
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'];

$result = mysqli_query($conn, "SELECT id FROM favorites WHERE user_id=$user_id AND property_id=$property_id");

    $action = "";
    if (mysqli_num_rows($result) > 0) {
        mysqli_query($conn, "DELETE FROM favorites WHERE user_id=$user_id AND property_id=$property_id");
        $action = "removed";
        $_SESSION['success_msg'] = "Property removed from favorites.";
    } else {
        if (mysqli_query($conn, "INSERT INTO favorites (user_id, property_id) VALUES ($user_id, $property_id)")) {
            $action = "added";
            $_SESSION['success_msg'] = "Property added to favorites!";
        } else {
            $action = "error";
            $_SESSION['error_msg'] = "Error saving favorite. Please try again.";
        }
    }

    // Check if AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'action' => $action]);
        exit();
    }

    header("Location: ../property_detail.php?id=$property_id");
    exit();
?>
