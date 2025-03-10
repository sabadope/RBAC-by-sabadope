<?php
session_start();
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

<h2>Welcome, <?php echo $_SESSION["name"]; ?></h2>

<!-- Role-Based Buttons -->
<?php if ($role === "Admin"): ?>
    <button>Admin</button>
    <button>Manager</button>
    <button>User</button>
<?php elseif ($role === "Manager"): ?>
    <button>Manager</button>
    <button>User</button>
<?php elseif ($role === "User"): ?>
    <button>User</button>
<?php endif; ?>

<!-- File Upload & View Files -->
<a href="upload.php">Upload File</a>
<a href="view_files.php">View Files</a>
<a href="login.php">Logout</a>

</body>
</html>
