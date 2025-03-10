<?php
session_start();
include '../src/config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"];
$user_id = $_SESSION["user_id"];

// Fetch files based on role
$query = "SELECT users.name, files.filename, files.filepath FROM files 
          JOIN users ON files.user_id = users.id";

if ($role === "User") {
    $query .= " WHERE users.id = $user_id"; // Users see only their own files
} elseif ($role === "Manager") {
    $query .= " WHERE users.role IN ('User', 'Manager')"; // Managers see their own & users' files
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
  <link rel="icon" type="image/png" href="assets/img/fav-logo.png">
  <!-- Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Uploaded Files</h2>

    <!-- Show success or error messages -->
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
                    <td><a href="<?php echo htmlspecialchars($row["filepath"]); ?>" download class="btn btn-primary">Download</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
