<?php
session_start();
require_once '../src/config.php'; // Database connection
require_once '../src/auth.php'; // Authentication handling

// Ensure only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../public/index.php');
    exit();
}

// Fetch total users count
$stmt = $conn->query("SELECT COUNT(*) as user_count FROM users");
$userCount = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// Fetch total files count
$stmt = $conn->query("SELECT COUNT(*) as file_count FROM files");
$fileCount = $stmt->fetch(PDO::FETCH_ASSOC)['file_count'];

// Fetch recent uploads (latest 5 files)
$stmt = $conn->query("SELECT users.name, files.filename, files.uploaded_at FROM files 
                      JOIN users ON files.user_id = users.id ORDER BY files.uploaded_at DESC LIMIT 5");
$recentFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>

    <div class="stats">
        <p>Total Users: <?php echo $userCount; ?></p>
        <p>Total Files Uploaded: <?php echo $fileCount; ?></p>
    </div>
    
    <h2>Recent Uploads</h2>
    <ul>
        <?php foreach ($recentFiles as $file): ?>
            <li><?php echo htmlspecialchars($file['name']) . ' uploaded ' . htmlspecialchars($file['filename']) . ' on ' . $file['uploaded_at']; ?></li>
        <?php endforeach; ?>
    </ul>
    
    <a href="manage_users.php">Manage Users</a> | <a href="manage_files.php">Manage Files</a>
    <br><br>
    <a href="../public/logout.php">Logout</a>
</body>
</html>
