<?php
session_start();
include '../src/config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $uploadDir = "../uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["file"]["name"]);
    $file_extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Get file extension
    $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'png']; // Allowed file types

    // Validate file type
    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Invalid file type! Only PDF, DOC, DOCX, JPG, and PNG are allowed.";
        exit();
    }

    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $fileName, $targetFilePath);

        if ($stmt->execute()) {
            echo "File uploaded successfully.";
        } else {
            echo "Error saving file to database.";
        }

        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
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
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
