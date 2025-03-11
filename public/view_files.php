<?php
session_start();
include '../src/config.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Define accessible file scopes based on roles
$file_access = [
    "Admin" => "'Admin', 'Manager', 'User'",  // Admin can see all files
    "Manager" => "'Manager', 'User'",         // Manager can see Manager & User files
    "User" => "'User'"                        // User can see only their own files
];

if (!isset($file_access[$role])) {
    die("Invalid role.");
}

// Fetch files based on role
$sql = "SELECT files.filename, files.filepath 
        FROM files 
        JOIN users ON files.user_id = users.id 
        WHERE users.role IN ({$file_access[$role]})";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Files</title>
</head>
<body>

<h2>Your Accessible Files</h2>
<ul>
    <?php while ($row = $result->fetch_assoc()): ?>
        <li>
            <a href="<?php echo htmlspecialchars($row['filepath']); ?>" target="_blank">
                <?php echo htmlspecialchars($row['filename']); ?>
            </a>
        </li>
    <?php endwhile; ?>
</ul>

</body>
</html>

<?php
$stmt->close();
?>
