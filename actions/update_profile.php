<?php
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

$check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != $user_id");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['profile_error'] = "This email is already registered to another account.";
    header("Location: ../edit_profile.php");
    exit();
}

$query = "UPDATE users SET name='$name', email='$email', phone='$phone'";

// Handle avatar upload
if (isset($_FILES['avatar']) && $_FILES['avatar']['name'] != "") {
    $file = $_FILES['avatar'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!in_array($file['type'], $allowed)) {
        $_SESSION['profile_error'] = "Invalid image format. Use JPG, PNG, WebP or GIF.";
        header("Location: ../edit_profile.php");
        exit();
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['profile_error'] = "Image is too large. Maximum 5MB allowed.";
        header("Location: ../edit_profile.php");
        exit();
    }

    $upload_dir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "avatar_" . $user_id . "_" . time() . "." . $ext;
    $target = $upload_dir . $filename;
    $db_path = "uploads/avatars/" . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        // Delete old avatar file if exists
        $old = mysqli_query($conn, "SELECT avatar FROM users WHERE id=$user_id");
        $old_row = mysqli_fetch_assoc($old);
        if (!empty($old_row['avatar'])) {
            $old_file = __DIR__ . '/../' . $old_row['avatar'];
            if (file_exists($old_file)) unlink($old_file);
        }
        $query .= ", avatar='$db_path'";
        $_SESSION['avatar'] = $db_path; // Update session with new avatar
    }
}

// Handle password change
if ($new_password != "") {
    if ($new_password !== $confirm_password) {
        $_SESSION['profile_error'] = "New passwords do not match.";
        header("Location: ../edit_profile.php");
        exit();
    }
    if (strlen($new_password) < 6) {
        $_SESSION['profile_error'] = "Password must be at least 6 characters.";
        header("Location: ../edit_profile.php");
        exit();
    }
    $safe_password = mysqli_real_escape_string($conn, $new_password);
    $query .= ", password='$safe_password'";
}

$query .= " WHERE id=$user_id";
$result = mysqli_query($conn, $query);

if ($result) {
    $_SESSION['user_name'] = $name;
    $_SESSION['profile_success'] = "Profile updated successfully!";
} else {
    $_SESSION['profile_error'] = "Failed to update profile. Please try again.";
}

header("Location: ../edit_profile.php");
exit();
?>
