<?php
session_start();
include '../src/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Define upload directory based on role
$uploadDir = "../uploads/" . strtolower($role) . "/";

// Ensure directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$error = "";
$success = "";

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file = $_FILES["file"];
    $fileName = basename($file["name"]);
    $filePath = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    // Allowed file types (prevent malicious uploads)
    $allowedTypes = ["jpg", "jpeg", "png", "pdf", "txt", "docx"];

    if (!in_array($fileType, $allowedTypes)) {
        $error = "❌ Invalid file type! Only JPG, PNG, PDF, TXT, and DOCX are allowed.";
    } elseif ($file["size"] > 2 * 1024 * 1024) { // Limit to 2MB
        $error = "❌ File is too large! Max size: 2MB.";
    } elseif (move_uploaded_file($file["tmp_name"], $filePath)) {
        $success = "✅ File uploaded successfully!";
        // Store file info in the database
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $fileName, $filePath);
        $stmt->execute();
        $stmt->close();
    } else {
        $error = "❌ Error uploading file. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>File Upload</span>
        <a href="dashboard.php" class="close-button">⬅️ Back</a>
    </div>

    <div class="window-content">
        <h2>Upload a File</h2>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">⬆️ Upload</button>
        </form>
        
        <p><a href="view_files.php">📁 View Uploaded Files</a></p>
    </div>
</div>

</body>
</html>
