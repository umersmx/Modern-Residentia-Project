<?php
$conn = mysqli_connect("127.0.0.1", "root", "", "modern_residentia");

if (!$conn) {
    die("Connection Failed");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
