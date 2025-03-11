<?php
session_start();
include '../src/config.php';
include '../src/csrf.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Define upload directories based on roles
$upload_dirs = [
    "Admin" => "../uploads/admin/",
    "Manager" => "../uploads/manager/",
    "User" => "../uploads/user/"
];

if (!isset($upload_dirs[$role])) {
    die("Invalid role.");
}

$target_dir = $upload_dirs[$role];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validateCsrfToken($_POST["csrf_token"])) {
        die("CSRF token validation failed.");
    }

    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    // Allowed file types
    $allowed_types = ["jpg", "jpeg", "png", "pdf", "docx"];
    $max_size = 2 * 1024 * 1024; // 2MB max file size

    $file_name = basename($_FILES["file"]["name"]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = time() . "_" . $user_id . "." . $file_ext;
    $target_file = $target_dir . $new_file_name;

    // Validate file type
    if (!in_array($file_ext, $allowed_types)) {
        die("Invalid file type.");
    }

    // Validate file size
    if ($_FILES["file"]["size"] > $max_size) {
        die("File too large.");
    }

    // Move uploaded file securely
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $new_file_name, $target_file);
        $stmt->execute();
        $stmt->close();

        echo "File uploaded successfully.";
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

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

</body>
</html>
