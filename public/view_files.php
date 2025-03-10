<?php
session_start();
include '../src/config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"];
$user_id = $_SESSION["user_id"];

$query = "SELECT users.name, files.filename, files.filepath FROM files 
          JOIN users ON files.user_id = users.id";

if ($role === "User") {
    // Users see only their own files
    $query .= " WHERE users.id = $user_id";
} elseif ($role === "Manager") {
    // Managers see all users' files + their own
    $query .= " WHERE users.role IN ('User', 'Manager')";
} elseif ($role === "Admin") {
    // Admin sees all files (no restriction)
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - View Files</title>
  <link rel="icon" type="image/png" href="assets/img/fav-logo.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<h2>Uploaded Files</h2>
<table border="1" class="table table-striped">
    <tr>
        <th>Uploaded By</th>
        <th>File Name</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["name"]; ?></td>
            <td><?php echo $row["filename"]; ?></td>
            <td>
                <a href="<?php echo $row["filepath"]; ?>" download>Download</a>
                <?php if ($role === "Admin"): ?>
                    | <a href="delete_file.php?file=<?php echo $row['filename']; ?>">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
