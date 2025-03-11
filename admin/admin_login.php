<?php
session_start();
include '../src/config.php';

// If already logged in, redirect to admin dashboard
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'Admin') {
    header("Location: dashboard.php");
    exit();
}

// Initialize error message
$error = "";

// CSRF Token Handling
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ensure default Admin exists
$admin_email = "admin@gmail.com";
$admin_password = "admin1230";

// Check if the admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->store_result();

// If no admin exists, create one
if ($stmt->num_rows === 0) {
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES ('Administrator', ?, ?, 'Admin', NOW())");
    $stmt->bind_param("ss", $admin_email, $hashed_password);
    $stmt->execute();
}
$stmt->close();

// Process Admin Login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $csrf_token = $_POST['csrf_token'];

    // Validate CSRF Token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token");
    }

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        if ($email === "admin@gmail.com") {
            $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user_id, $name, $hashed_password, $role);
                $stmt->fetch();

                if ($role === 'Admin' && password_verify($password, $hashed_password)) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['name'] = $name;
                    $_SESSION['role'] = $role;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            $error = "Only admin can log in here.";
        }
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
        <h2>Admin Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="admin_login.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label>Email:</label>
            <input type="email" name="email" value="admin@gmail.com" required>
            
            <label>Password:</label>
            <input type="password" name="password" value="admin1230" required>

            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>
