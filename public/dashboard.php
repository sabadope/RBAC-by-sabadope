<?php
session_start();
include '../src/session.php'; // Secure session handling

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"];
$name = htmlspecialchars($_SESSION["name"]); // Prevent XSS
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome, <?php echo $name; ?></h2>

<!-- Role-Based Navigation -->
<?php if ($role === "Admin"): ?>
    <a href="admin_dashboard.php">Admin Panel</a>
    <a href="view_files.php">View All Files</a>
<?php endif; ?>

<?php if ($role === "Manager" || $role === "Admin"): ?>
    <a href="view_files.php">View Manager & User Files</a>
<?php endif; ?>

<?php if ($role === "User" || $role === "Manager" || $role === "Admin"): ?>
    <a href="upload.php">Upload File</a>
    <a href="view_files.php">View My Files</a>
<?php endif; ?>

<!-- Logout -->
<form method="POST" action="logout.php">
    <button type="submit">Logout</button>
</form>

</body>
</html>
