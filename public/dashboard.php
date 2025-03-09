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
</head>
<body>

<h2>Welcome, <?php echo $_SESSION["name"]; ?></h2>

<!-- Toggle Buttons Based on Role -->
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

<a href="logout.php">Logout</a>

</body>
</html>
