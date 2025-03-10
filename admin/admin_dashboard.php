<?php
session_start();
include '../src/config.php';

// Ensure only admin can access
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["role"] !== "Admin") {
    header("Location: ../login.php");
    exit();
}

$user_name = $_SESSION["user_name"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/png" href="../assets/img/fav-logo.png">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
    <p>This is the admin panel.</p>

    <div class="mt-3">
        <a href="upload.php" class="btn btn-info">Upload File</a>
        <a href="view_files.php" class="btn btn-warning">View Files</a>
        <a href="../admin_logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>
