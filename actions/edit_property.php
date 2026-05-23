<?php
include '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$property_id = intval($_POST['property_id']);
$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$price = $_POST['price'];
$location = mysqli_real_escape_string($conn, $_POST['location']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$type = $_POST['type'];
$bedrooms = $_POST['bedrooms'];
$bathrooms = $_POST['bathrooms'];
$area_sqft = $_POST['area_sqft'];
$status = $_POST['status'];

$allowed_statuses = array('pending', 'approved', 'rented');
if (!in_array($status, $allowed_statuses)) {
    $status = 'pending';
}

$verify = mysqli_query($conn, "SELECT id FROM properties WHERE id=$property_id AND owner_id=$user_id");
if (mysqli_num_rows($verify) == 0) {
    $_SESSION['error_msg'] = "Property not found or unauthorized.";
    header("Location: ../owner_dashboard.php");
    exit();
}

$query = "UPDATE properties SET 
          title='$title', description='$description', price='$price', 
          location='$location', city='$city', type='$type', 
          bedrooms='$bedrooms', bathrooms='$bathrooms', area_sqft='$area_sqft', 
          status='$status' 
          WHERE id=$property_id AND owner_id=$user_id";

$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['edit_success'] = "Property updated successfully!";
} else {
    $_SESSION['edit_error'] = "Failed to update property. Error: " . mysqli_error($conn);
}

header("Location: ../edit_property.php?id=$property_id");
exit();
?>
