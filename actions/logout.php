<?php include '../config/db_connection.php'; ?>

<?php
session_destroy();
header("Location: ../login.php");
?>
