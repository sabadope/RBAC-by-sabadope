<?php
session_start();
include '../src/config.php';
include '../src/session.php'; // Ensures user authentication

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Role-based file access
$where_clause = "";
if ($role === "Admin") {
    $where_clause = ""; // Admin sees all files
} elseif ($role === "Manager") {
    $where_clause = "WHERE u.role IN ('Manager', 'User')"; // Manager sees only Manager & User files
} else {
    $where_clause = "WHERE f.user_id = $user_id"; // Regular User sees only their own files
}

// Fetch files based on role
$sql = "SELECT f.id, f.filename, f.filepath, u.name AS uploader, u.role 
        FROM files f 
        JOIN users u ON f.user_id = u.id 
        $where_clause 
        ORDER BY f.uploaded_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Files</title>
</head>
<body>

<h2>Uploaded Files</h2>

<table border="1">
    <tr>
        <th>Filename</th>
        <th>Uploader</th>
        <th>Role</th>
        <th>Download</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?php echo htmlspecialchars($row['filename']); ?></td>
        <td><?php echo htmlspecialchars($row['uploader']); ?></td>
        <td><?php echo htmlspecialchars($row['role']); ?></td>
        <td><a href="<?php echo htmlspecialchars($row['filepath']); ?>" download>Download</a></td>
    </tr>
    <?php endwhile; ?>

</table>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
