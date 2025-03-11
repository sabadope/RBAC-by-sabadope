<?php
session_start();
include '../src/config.php';
include '../src/session.php'; // Ensures user authentication

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Windows 95 Theme -->
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>Dashboard - <?php echo htmlspecialchars($role); ?></span>
        <a href="logout.php" class="close-button">X</a>
    </div>

    <div class="window-content">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        <p>Your Role: <strong><?php echo htmlspecialchars($role); ?></strong></p>

        <hr>

        <?php if ($role === "Admin") : ?>
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="view_files.php">View All Files</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
            </ul>
        <?php endif; ?>

        <?php if ($role === "Manager" || $role === "Admin") : ?>
            <h3>Manager Panel</h3>
            <ul>
                <li><a href="upload.php">Upload Files</a></li>
                <li><a href="view_files.php">View Manager & User Files</a></li>
            </ul>
        <?php endif; ?>

        <h3>User Panel</h3>
        <ul>
            <li><a href="upload.php">Upload Files</a></li>
            <li><a href="view_files.php">View My Files</a></li>
        </ul>

        <hr>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</div>

</body>
</html>
