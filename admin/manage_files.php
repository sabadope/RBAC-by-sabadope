<?php
session_start();
include '../config.php'; // Database connection

// Check user role
if (!isset($_SESSION['role'])) {
    echo "Access denied.";
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Delete file logic
if (isset($_GET['delete_file'])) {
    $file_id = $_GET['delete_file'];

    // Check if user is admin or owns the file
    if ($user_role === 'admin') {
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $file_id, $user_id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: manage_files.php");
    exit;
}

// Fetch files based on role
if ($user_role === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM files");
} else {
    $stmt = $conn->prepare("SELECT * FROM files WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>main</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/img/fav-logo.png">
  <!-- Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <h2>Manage Files</h2>
    <table border="1" class="table table-striped">
        <tr>
            <th>Filename</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['filename'] ?></td>
            <td>
                <a href="manage_files.php?delete_file=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
