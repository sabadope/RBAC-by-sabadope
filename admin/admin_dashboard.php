<?php
session_start();
include '../src/config.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/img/fav-logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
    <p>This is the admin panel.</p>

    <!-- Role-Based Buttons -->
    <?php if ($role === "Admin"): ?>
        <button class="btn btn-primary">Admin</button>
        <button class="btn btn-secondary">Manager</button>
        <button class="btn btn-success">User</button>
    <?php elseif ($role === "Manager"): ?>
        <button class="btn btn-secondary">Manager</button>
        <button class="btn btn-success">User</button>
    <?php elseif ($role === "User"): ?>
        <button class="btn btn-success">User</button>
    <?php endif; ?>

    <a href="upload.php" class="btn btn-info">Upload File</a>
    <a href="view_files.php" class="btn btn-warning">View Files</a>
    <a href="admin_logout.php" class="btn btn-danger">Logout</a>
</div>

</body>
</html>
