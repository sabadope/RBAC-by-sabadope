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

// Role-based content
$dashboard_title = "";
$dashboard_content = "";

switch ($role) {
    case 'Admin':
        $dashboard_title = "Admin Dashboard";
        $dashboard_content = "<p>Welcome, Admin! You can manage users and view all uploaded files.</p>";
        break;
    case 'Manager':
        $dashboard_title = "Manager Dashboard";
        $dashboard_content = "<p>Welcome, Manager! You can view and manage manager/user files.</p>";
        break;
    case 'User':
        $dashboard_title = "User Dashboard";
        $dashboard_content = "<p>Welcome, $name! You can upload and view your own files.</p>";
        break;
    default:
        session_destroy();
        header("Location: login.php");
        exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $dashboard_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span><?php echo $dashboard_title; ?></span>
        <a href="logout.php" class="close-button">Logout</a>
    </div>

    <div class="window-content">
        <h2><?php echo $dashboard_title; ?></h2>
        <?php echo $dashboard_content; ?>

        <ul>
            <li><a href="upload.php">Upload Files</a></li>
            <li><a href="view_files.php">View Files</a></li>
        </ul>
    </div>
</div>

</body>
</html>
