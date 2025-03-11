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
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Define role-based access
$role_conditions = [
    'Admin' => "1", // Admin sees all files
    'Manager' => "users.role IN ('Manager', 'User')", // Manager sees Manager & User files
    'User' => "users.id = $user_id" // User sees only their files
];

$condition = $role_conditions[$role] ?? "users.id = $user_id";

// Fetch files based on role
$query = "
    SELECT files.id, files.filename, files.filepath, users.name AS uploader, files.uploaded_at 
    FROM files
    INNER JOIN users ON files.user_id = users.id
    WHERE $condition
    ORDER BY files.uploaded_at DESC
";

$result = $conn->query($query);

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $file_id = $_POST['file_id'];
    
    // Get file details
    $stmt = $conn->prepare("SELECT filepath, user_id FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->bind_result($filepath, $file_owner);
    $stmt->fetch();
    $stmt->close();
    
    // Ensure user can delete this file
    if ($role === 'Admin' || $user_id == $file_owner) {
        if (unlink($filepath)) {
            $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
            $stmt->bind_param("i", $file_id);
            $stmt->execute();
            $stmt->close();
            $message = "✅ File deleted successfully.";
        } else {
            $message = "❌ Error deleting file.";
        }
    } else {
        $message = "❌ Unauthorized action.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Files</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>Uploaded Files</span>
        <a href="dashboard.php" class="close-button">Back</a>
    </div>

    <div class="window-content">
        <h2>Uploaded Files</h2>
        
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Uploader</th>
                    <th>Uploaded At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><a href="<?php echo $row['filepath']; ?>" target="_blank"><?php echo $row['filename']; ?></a></td>
                        <td><?php echo $row['uploader']; ?></td>
                        <td><?php echo $row['uploaded_at']; ?></td>
                        <td>
                            <?php if ($role === 'Admin' || $user_id == $row['user_id']): ?>
                                <form action="view_files.php" method="post">
                                    <input type="hidden" name="file_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
