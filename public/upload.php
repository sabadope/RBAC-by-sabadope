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

// Define role-based upload directory
$upload_dirs = [
    'Admin' => '../uploads/admin/',
    'Manager' => '../uploads/manager/',
    'User' => '../uploads/user/'
];

$upload_dir = $upload_dirs[$role] ?? '../uploads/user/';

// Ensure directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// File upload handling
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $filepath = $upload_dir . $filename;
    
    // File validation
    $allowed_types = ['jpg', 'png', 'pdf', 'txt', 'docx'];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        $message = "❌ Invalid file type. Allowed: " . implode(", ", $allowed_types);
    } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        $message = "❌ File too large. Max: 2MB.";
    } elseif (file_exists($filepath)) {
        $message = "❌ File already exists. Rename and try again.";
    } else {
        // Move file & insert into database
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath, uploaded_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $user_id, $filename, $filepath);
            $stmt->execute();
            $stmt->close();
            $message = "✅ File uploaded successfully!";
        } else {
            $message = "❌ Error uploading file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="window">
    <div class="title-bar">
        <span>Upload File</span>
        <a href="dashboard.php" class="close-button">Back</a>
    </div>

    <div class="window-content">
        <h2>Upload File</h2>
        <p>Allowed file types: jpg, png, pdf, txt, docx (Max: 2MB)</p>
        
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</div>

</body>
</html>
