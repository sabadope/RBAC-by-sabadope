<?php
// Force logout to re-authenticate every time
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Restricted Admin Access"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Access denied. Only admins can access this page.";
    exit;
}

// Check if the provided credentials are correct
$valid_username = 'admin'; // Change this if you have multiple admins
$valid_password = 'admin1230'; // Replace with the correct password

if ($_SERVER['PHP_AUTH_USER'] !== $valid_username || $_SERVER['PHP_AUTH_PW'] !== $valid_password) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Access denied. Only admins can access this page.";
    exit;
}
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

<h1>Welcome, Admin!</h1>
<p>This is the admin panel.</p>

</body>
</html>
