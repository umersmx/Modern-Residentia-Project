<?php include '../config/db_connection.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
        header("Location: ../login.php");
        exit();
    }

    $owner_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $city = $_POST['city'];
    $type = $_POST['type'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area_sqft = $_POST['area_sqft'];
    $amenities = '["WiFi", "Parking", "Security"]';

    function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source);
        else return false;

        $width = imagesx($image);
        $height = imagesy($image);
        if ($width > 1200) {
            $new_width = 1200;
            $new_height = floor($height * ($new_width / $width));
            $tmp = imagecreatetruecolor($new_width, $new_height);
            if ($info['mime'] == "image/png") {
                imagealphablending($tmp, false);
                imagesavealpha($tmp, true);
                $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
                imagefilledrectangle($tmp, 0, 0, $new_width, $new_height, $transparent);
            }
            imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $image = $tmp;
        }

        if ($info['mime'] == 'image/jpeg') imagejpeg($image, $destination, $quality);
        elseif ($info['mime'] == 'image/png') imagepng($image, $destination, 8);
        elseif ($info['mime'] == 'image/gif') imagegif($image, $destination);
        return true;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO properties (owner_id, title, description, price, location, city, type, bedrooms, bathrooms, area_sqft, amenities, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, "ississsiiis", $owner_id, $title, $description, $price, $location, $city, $type, $bedrooms, $bathrooms, $area_sqft, $amenities);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $property_id = mysqli_insert_id($conn);

        $img_count = isset($_FILES['images']) && $_FILES['images']['name'][0] != "" ? count($_FILES['images']['name']) : 0;
        if ($img_count < 3 || $img_count > 6) {
            mysqli_query($conn, "DELETE FROM properties WHERE id=$property_id");
            $_SESSION['error_msg'] = "Please upload between 3 and 6 images.";
            header("Location: ../owner_dashboard.php");
            exit();
        }

        $upload_path = __DIR__ . '/../uploads/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);

        $img_stmt = mysqli_prepare($conn, "INSERT INTO property_images (property_id, image_path, is_primary) VALUES (?, ?, ?)");

        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $new_filename = "prop_" . $property_id . "_" . time() . "_" . $key . "." . $ext;
            $target_file = $upload_path . $new_filename;
            $db_path = "uploads/" . $new_filename;

            // Compress and save
            if (compressImage($tmp_name, $target_file, 75)) {
                $is_primary = ($key == 0) ? 1 : 0;
                mysqli_stmt_bind_param($img_stmt, "isi", $property_id, $db_path, $is_primary);
                mysqli_stmt_execute($img_stmt);
            }
        }

        $_SESSION['success_msg'] = "Property listing created successfully! It is currently pending admin approval.";
    } else {
        $_SESSION['error_msg'] = "Failed to create property listing. Error: " . mysqli_error($conn);
    }

    header("Location: ../owner_dashboard.php");
    exit();
}
?>
