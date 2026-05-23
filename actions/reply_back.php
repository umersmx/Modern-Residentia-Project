<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'renter') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = (int)$_POST['property_id'];
    $sender_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (!empty($property_id) && !empty($message)) {
        // We create a new inquiry entry for this reply to continue the conversation thread
        $query = "INSERT INTO inquiries (property_id, sender_id, message, is_read, created_at) 
                  VALUES ($property_id, $sender_id, '$message', 0, NOW())";
                  
        if (mysqli_query($conn, $query)) {
            $_SESSION['success_msg'] = "Your reply was sent to the property owner.";
        } else {
            $_SESSION['error_msg'] = "Error sending your reply. Please try again.";
        }
    } else {
        $_SESSION['error_msg'] = "Message cannot be empty.";
    }
}

header("Location: ../renter_dashboard.php#inquiries");
exit();
?>
