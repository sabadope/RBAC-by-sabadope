<?php
session_start();

// Hardcoded Admin Credentials
$admin_email = "admin@gmail.com";
$admin_password = "admin1230"; // This is a plain-text match for now

$error = "";

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

// CSRF Token Handling
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process Login Form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $csrf_token = $_POST['csrf_token'];

    // Validate CSRF Token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token");
    }

    // Validate Admin Credentials
    if ($email === $admin_email && $password === $admin_password) {
        // Admin Authenticated
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $admin_email;

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>Admin Login</span>
        <a href="../public/index.php" class="close-button">X</a>
    </div>

    <div class="window-content">
        <h2>Admin Panel</h2>

        <?php if (!empty($error)): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="admin_login.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>
