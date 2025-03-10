<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Default admin credentials for login page
    $valid_email = "admin@gmail.com";
    $valid_password = "admin1230";

    if ($email === $valid_email && $password === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid email or password! Please try again.');</script>";
    }
}
?>
