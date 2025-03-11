<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Logout handling
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>Admin Dashboard</span>
        <a href="?logout=true" class="close-button">X</a>
    </div>

    <div class="window-content">
        <h2>Welcome, Admin!</h2>
        <p>This is the Admin Dashboard.</p>

        <a href="admin_upload.php">Upload</a>
        <a href="view_files.php">View Files</a>
        <a href="?logout=true" class="button">Logout</a>
    </div>
</div>

</body>
</html>
