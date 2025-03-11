<?php
session_start();
require_once '../src/config.php';
require_once '../src/auth.php';

// Ensure only Admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../public/index.php');
    exit();
}

// Fetch all uploaded files
$sql = "SELECT f.id, f.filename, f.filepath, f.uploaded_at, u.name AS uploader, u.role AS uploader_role 
        FROM files f 
        JOIN users u ON f.user_id = u.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle file deletion
if (isset($_POST['delete'])) {
    $fileId = $_POST['file_id'];
    $filePath = $_POST['file_path'];

    $deleteSql = "DELETE FROM files WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('i', $fileId);
    
    if ($deleteStmt->execute()) {
        unlink($filePath); // Delete file from the server
        $_SESSION['message'] = "File deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete file.";
    }
    header('Location: manage_files.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Files - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Manage Uploaded Files</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <p class="success-msg"> <?php echo $_SESSION['message']; unset($_SESSION['message']); ?> </p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error-msg"> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?> </p>
    <?php endif; ?>
    
    <table border="1">
        <thead>
            <tr>
                <th>Filename</th>
                <th>Uploader</th>
                <th>Role</th>
                <th>Uploaded At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?php echo htmlspecialchars($file['filename']); ?></td>
                    <td><?php echo htmlspecialchars($file['uploader']); ?></td>
                    <td><?php echo htmlspecialchars($file['uploader_role']); ?></td>
                    <td><?php echo htmlspecialchars($file['uploaded_at']); ?></td>
                    <td>
                        <a href="../<?php echo $file['filepath']; ?>" download>Download</a>
                        |
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                            <input type="hidden" name="file_path" value="<?php echo $file['filepath']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>