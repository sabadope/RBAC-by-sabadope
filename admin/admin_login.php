<?php
session_start();

// Set security authentication credentials (Basic Auth)
$auth_username = "admin";  
$auth_password = "admin1230";  

// Force re-authentication by clearing previous session if user clicks "Admin Login"
if (isset($_GET['force_auth'])) {
    header('HTTP/1.0 401 Unauthorized'); // Forces the authentication prompt to appear again
}

// Check if authentication credentials are provided
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Unauthorized access. Please enter the correct credentials.";
    exit;
}

// Verify entered credentials
if ($_SERVER['PHP_AUTH_USER'] !== $auth_username || $_SERVER['PHP_AUTH_PW'] !== $auth_password) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Invalid credentials. Please try again.";
    exit;
}

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Admin Login</h2>

<?php if (isset($error_message)) : ?>
    <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Login</button>
</form>

<a href="../public/login.php">Back to User Login</a>

</body>
</html>
