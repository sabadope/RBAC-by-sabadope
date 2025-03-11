<?php
session_start();
include '../src/config.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all uploaded files from Users and Managers
$query = "
    SELECT files.id, files.filename, files.filepath, users.name AS uploader, users.role AS uploader_role, files.uploaded_at 
    FROM files
    INNER JOIN users ON files.user_id = users.id
    WHERE users.role IN ('User', 'Manager')
    ORDER BY files.uploaded_at DESC
";
$result = $conn->query($query);

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $file_id = $_POST['file_id'];

    // Get file details
    $stmt = $conn->prepare("SELECT filepath FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->bind_result($filepath);
    $stmt->fetch();
    $stmt->close();

    // Delete file from server and database
    if (unlink($filepath)) {
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $stmt->close();
        $message = "✅ File deleted successfully.";
    } else {
        $message = "❌ Error deleting file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Files</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>📁 View Uploaded Files</span>
        <a href="admin_dashboard.php" class="close-button">⬅️ Back</a>
    </div>

    <div class="window-content">
        <h2>Uploaded Files (Users & Managers)</h2>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Uploader</th>
                    <th>Role</th>
                    <th>Uploaded At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><a href="<?php echo $row['filepath']; ?>" target="_blank"><?php echo $row['filename']; ?></a></td>
                        <td><?php echo $row['uploader']; ?></td>
                        <td><?php echo $row['uploader_role']; ?></td>
                        <td><?php echo $row['uploaded_at']; ?></td>
                        <td>
                            <form action="view_files.php" method="post">
                                <input type="hidden" name="file_id" value="<?php echo $row['id']; ?>">
                                <td><a href="delete_file.php?id=<?php echo $file['id']; ?>" onclick="return confirm('Are you sure you want to delete this file?');">🗑️ Delete</a></td>

                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
