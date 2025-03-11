<?php
session_start();
include '../src/config.php';
include '../src/session.php'; // Ensures user authentication

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Get user role

// Define allowed file types and size limit
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'docx'];
$max_file_size = 2 * 1024 * 1024; // 2MB

// Role-based upload directory
$upload_dir = "../uploads/$role/";

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0775, true); // Create directory if it doesn’t exist
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];
    $file_size = $_FILES["file"]["size"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($file_ext, $allowed_types)) {
        die("Invalid file type. Allowed: " . implode(", ", $allowed_types));
    }

    // Validate file size
    if ($file_size > $max_file_size) {
        die("File is too large. Max size: 2MB.");
    }

    // Generate a unique file name
    $safe_filename = preg_replace("/[^a-zA-Z0-9-_\.]/", "", basename($file_name));
    $new_file_name = uniqid() . "_" . $safe_filename;
    $file_path = $upload_dir . $new_file_name;

    // Move uploaded file
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Save file info to database
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $new_file_name, $file_path);
        
        if ($stmt->execute()) {
            echo "File uploaded successfully!";
        } else {
            echo "Error saving file info to the database.";
        }
        $stmt->close();
    } else {
        echo "File upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload File</title>
</head>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
