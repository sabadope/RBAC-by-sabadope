<?php
session_start();
include '../src/config.php';

// Ensure only admin can upload files
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["role"] !== "Admin") {
    header("Location: admin_dashboard.php");
    exit();
}

// Check if admin_id is stored in the session
$admin_id = $_SESSION["admin_id"] ?? 1; // Use session admin_id, default to 1 if not set

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $uploadDir = "../uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Insert file details into the database
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $admin_id, $fileName, $targetFilePath);
        
        if ($stmt->execute()) {
            $_SESSION["success"] = "File uploaded successfully.";
        } else {
            $_SESSION["error"] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION["error"] = "Error uploading file.";
    }

    header("Location: view_files.php");
    exit();
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

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>
<a href="admin_dashboard.php">Back to Dashboard</a>

</body>
</html>
