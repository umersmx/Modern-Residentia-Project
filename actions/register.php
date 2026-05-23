<?php include '../config/db_connection.php'; ?>

<?php
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Password Strength Validation (1 uppercase, 1 number, 1 special character, at least 8 chars)
    if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[A-Z]+#", $password) || !preg_match("#[\W]+#", $password)) {
        $_SESSION['error_msg'] = "Password must be at least 8 characters long, contain at least 1 number, 1 uppercase letter, and 1 special character.";
        header("Location: ../register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_msg'] = "Passwords do not match.";
        header("Location: ../register.php");
        exit();
    }

    // Check if email already exists using Prepared Statements
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['error_msg'] = "Email is already registered.";
        header("Location: ../register.php");
        exit();
    }
    mysqli_stmt_close($stmt);

    // Insert new user using Prepared Statements (Plain-text password as requested)
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $role);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // --- EMAIL NOTIFICATION LOGIC ---
        // To use PHPMailer, you need to install it via composer: composer require phpmailer/phpmailer
        // Example PHPMailer logic (using standard mail() as fallback for now):
        $subject = "Welcome to Modern Residentia!";
        $message = "
        <html>
        <head><title>Welcome to Modern Residentia</title></head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2>Hello $name,</h2>
            <p>Welcome to Modern Residentia! Your account has been successfully created as a <strong>$role</strong>.</p>
            <p>You can now log in and explore our platform.</p>
            <br>
            <p>Best regards,<br>The Modern Residentia Team</p>
        </body>
        </html>
        ";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@modernresidentia.com" . "\r\n";
        @mail($email, $subject, $message, $headers); // Silently fails if mail server is not configured

        $_SESSION['success_msg'] = "Registration successful! You can now login.";
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['error_msg'] = "Error registering account. Please try again.";
        header("Location: ../register.php");
        exit();
    }
}
?>
