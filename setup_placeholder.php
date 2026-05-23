<?php
/**
 * Fix: Download placeholder image (no GD extension needed)
 * Run once: http://localhost/WEB%20Project/setup_placeholder.php
 */

if (!is_dir('assets/images')) mkdir('assets/images', 0777, true);

echo "<h2>Downloading Placeholder Image...</h2>";

$url = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&q=80';
$img_data = @file_get_contents($url);

if ($img_data && file_put_contents('assets/images/property-placeholder.jpg', $img_data)) {
    echo "<p style='color:green;'>✅ Placeholder image created successfully!</p>";
} else {
    echo "<p style='color:red;'>❌ Download failed. Creating a simple HTML placeholder instead.</p>";
}

echo "<p><a href='index.php'>← Go to Homepage</a></p>";
?>
