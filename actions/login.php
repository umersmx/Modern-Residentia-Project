<?php include '../config/db_connection.php'; ?>

<?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Query Error"); // hide DB error
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verify plain-text password (as requested)
        if ($password !== $row['password']) {
            $_SESSION['error_msg'] = "Invalid Email or Password";
            header("Location: ../login.php");
            exit();
        }

        if ($row['is_active'] == 0) {
            $_SESSION['error_msg'] = "Your account has been deactivated. Contact admin.";
            header("Location: ../login.php");
            exit();
        }

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['avatar'] = $row['avatar'];

        if ($row['role'] == 'admin') {
            header("Location: ../admin_dashboard.php");
        } elseif ($row['role'] == 'owner') {
            header("Location: ../owner_dashboard.php");
        } else {
            header("Location: ../renter_dashboard.php");
        }
        exit();
    } else {
        $_SESSION['error_msg'] = "Invalid Email or Password";
        header("Location: ../login.php");
        exit();
    }
}
?>
