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
          JOIN users ON files.user_id = users.id ";

if ($role === "User") {
    $query .= "WHERE users.id = $user_id"; // Users see only their own files
} elseif ($role === "Manager") {
    $query .= "WHERE users.role IN ('User', 'Manager')"; // Managers see their own & users' files
} elseif ($role === "Admin") {
    // Admin sees all files (no extra condition needed)
}

$result = $conn->query($query);
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

<h2>Uploaded Files</h2>
<table border="1">
    <tr>
        <th>Uploaded By</th>
        <th>File Name</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["name"]; ?></td>
            <td><?php echo $row["filename"]; ?></td>
            <td><a href="<?php echo $row["filepath"]; ?>" download>Download</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
