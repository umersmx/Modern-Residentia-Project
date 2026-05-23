<?php
/**
 * Setup Script: Generates sample property images for the seed data.
 * Run this once in your browser: http://localhost/WEB%20Project/setup_images.php
 * After running, you can delete this file.
 */

// Create required directories
if (!is_dir('uploads')) mkdir('uploads', 0777, true);
if (!is_dir('assets/images')) mkdir('assets/images', 0777, true);

// Sample property images from free stock photo URLs (Picsum Photos)
$images = [
    'uploads/property_1_1.jpg' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80', // Luxury apartment
    'uploads/property_1_2.jpg' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&q=80', // Apartment bedroom
    'uploads/property_2_1.jpg' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80', // Studio apartment
    'uploads/property_3_1.jpg' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80', // House exterior
    'uploads/property_4_1.jpg' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80', // Hostel room
    'uploads/property_5_1.jpg' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80', // Office space
    'uploads/property_6_1.jpg' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80', // 2-bed flat
];

// Placeholder image
$placeholder_url = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80';

echo "<h2>Modern Residentia - Image Setup</h2>";
echo "<hr>";

$success = 0;
$failed = 0;

foreach ($images as $path => $url) {
    echo "<p>Downloading <strong>$path</strong>... ";
    
    $img_data = @file_get_contents($url);
    
    if ($img_data && file_put_contents($path, $img_data)) {
        echo "<span style='color:green;'>✅ Done!</span></p>";
        $success++;
    } else {
        // If download fails, create a colored placeholder
        $img = imagecreatetruecolor(800, 600);
        $colors = [
            imagecolorallocate($img, 52, 73, 94),
            imagecolorallocate($img, 41, 128, 185),
            imagecolorallocate($img, 39, 174, 96),
            imagecolorallocate($img, 192, 57, 43),
            imagecolorallocate($img, 142, 68, 173),
            imagecolorallocate($img, 243, 156, 18),
            imagecolorallocate($img, 22, 160, 133),
        ];
        $index = array_search($path, array_keys($images));
        imagefill($img, 0, 0, $colors[$index % count($colors)]);
        $white = imagecolorallocate($img, 255, 255, 255);
        $text = "Property Image " . ($index + 1);
        imagestring($img, 5, 320, 290, $text, $white);
        imagejpeg($img, $path, 90);
        imagedestroy($img);
        echo "<span style='color:orange;'>⚠️ Used placeholder</span></p>";
        $failed++;
    }
}

// Create the placeholder image
echo "<p>Creating <strong>assets/images/property-placeholder.jpg</strong>... ";
$ph = imagecreatetruecolor(800, 600);
$bg = imagecolorallocate($ph, 233, 236, 239);
$fg = imagecolorallocate($ph, 108, 117, 125);
imagefill($ph, 0, 0, $bg);
imagestring($ph, 5, 310, 280, "No Image Available", $fg);
imagejpeg($ph, 'assets/images/property-placeholder.jpg', 90);
imagedestroy($ph);
echo "<span style='color:green;'>✅ Done!</span></p>";

echo "<hr>";
echo "<h3>Summary: $success downloaded, $failed used placeholders</h3>";
echo "<p><strong>✅ Setup complete!</strong> You can now delete this file.</p>";
echo "<p><a href='index.php'>← Go to Homepage</a></p>";
?>
