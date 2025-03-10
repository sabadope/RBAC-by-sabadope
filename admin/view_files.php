<?php
session_start();
include '../src/config.php'; // Ensure database connection is included

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"];
$user_id = $_SESSION["user_id"];

// Admin should see ALL files
if ($role === "Admin") {
    $query = "SELECT users.name, files.id, files.filename, files.filepath FROM files 
              JOIN users ON files.user_id = users.id";
} elseif ($role === "Manager") {
    $query = "SELECT users.name, files.id, files.filename, files.filepath FROM files 
              JOIN users ON files.user_id = users.id 
              WHERE users.role IN ('User', 'Manager')";
} else { // Normal User
    $query = "SELECT users.name, files.id, files.filename, files.filepath FROM files 
              JOIN users ON files.user_id = users.id 
              WHERE users.id = $user_id";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Uploaded Files</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="../assets/img/fav-logo.png">
  <!-- Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Uploaded Files</h2>

    <!-- Success & Error Messages -->
    <?php if (isset($_SESSION["success"])): ?>
        <div class="alert alert-success"><?php echo $_SESSION["success"]; unset($_SESSION["success"]); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION["error"])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Uploaded By</th>
                <th>File Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["filename"]); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($row["filepath"]); ?>" download class="btn btn-primary">Download</a>
                        <?php if ($role === "Admin"): ?>
                            <a href="delete_file.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this file?')" 
                               class="btn btn-danger">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
