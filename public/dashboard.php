<?php
session_start();
include '../src/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $role);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span><?php echo $role; ?> Dashboard</span>
        <a href="logout.php" class="close-button">Logout</a>
    </div>

    <div class="window-content">
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        <p>You are logged in as: <strong><?php echo $role; ?></strong></p>

        <h3>Navigation</h3>
        <ul>
            <li><a href="view_files.php">📁 View Files</a></li>
            <li><a href="upload.php">⬆️ Upload Files</a></li>
        </ul>

        <?php if ($role === 'Admin'): ?>
            <h3>Admin Controls</h3>
            <ul>
                <li><a href="../admin/manage_users.php">👥 Manage Users</a></li>
            </ul>
        <?php endif; ?>

        <p><a href="logout.php">🚪 Logout</a></p>
    </div>
</div>

</body>
</html>
