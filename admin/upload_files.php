<?php
session_start();
include '../src/config.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle file upload
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $uploadDir = "../uploads/admin/"; // Admin upload directory
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if not exists
    }

    $filename = basename($_FILES["file"]["name"]);
    $targetFile = $uploadDir . $filename;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "pdf", "docx", "txt"];

    // Check file type
    if (!in_array($fileType, $allowedTypes)) {
        $message = "❌ Only JPG, PNG, PDF, DOCX, or TXT files are allowed.";
    } elseif ($_FILES["file"]["size"] > 5 * 1024 * 1024) { // 5MB limit
        $message = "❌ File size should not exceed 5MB.";
    } elseif (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        // Store file in the database
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $_SESSION['admin_id'], $filename, $targetFile);
        $stmt->execute();
        $stmt->close();
        $message = "✅ File uploaded successfully.";
    } else {
        $message = "❌ Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Files</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>📤 Upload Files</span>
        <a href="admin_dashboard.php" class="close-button">⬅️ Back</a>
    </div>

    <div class="window-content">
        <h2>Upload a File</h2>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="upload_files.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">📁 Upload</button>
        </form>
    </div>
</div>

</body>
</html>
