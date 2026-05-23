<?php
include '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'renter') {
    header("Location: ../login.php");
    exit();
}

$renter_id = $_SESSION['user_id'];
$location = mysqli_real_escape_string($conn, $_POST['desired_location']);
$type = mysqli_real_escape_string($conn, $_POST['property_type']);
$bedrooms = intval($_POST['min_bedrooms']);
$budget = floatval($_POST['max_budget']);
$notes = mysqli_real_escape_string($conn, $_POST['additional_notes']);

$query = "INSERT INTO property_requests (renter_id, desired_location, property_type, min_bedrooms, max_budget, additional_notes) 
          VALUES ($renter_id, '$location', '$type', $bedrooms, $budget, '$notes')";

if (mysqli_query($conn, $query)) {
    $_SESSION['request_success'] = "Your property request has been sent! An admin will contact you soon.";
} else {
    $_SESSION['request_error'] = "Failed to send request. Error: " . mysqli_error($conn);
}

header("Location: ../renter_dashboard.php");
exit();
?>
