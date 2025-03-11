<?php
session_start();
include '../src/config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"]; // ✅ Fix: Use actual role from session

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $filename = basename($_FILES["file"]["name"]);
    $filepath = "../uploads/" . $filename;

    // Ensure the uploads directory exists
    if (!is_dir("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $filepath)) {
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $filename, $filepath);
        
        if ($stmt->execute()) {
            $_SESSION["success"] = "File uploaded successfully!";
        } else {
            $_SESSION["error"] = "Failed to save file info. " . $stmt->error;  // 🔍 Debugging output
        }
        $stmt->close();
    } else {
        $_SESSION["error"] = "File upload failed.";
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
    <title>Admin - Upload File</title>
    <link rel="icon" type="image/png" href="../assets/img/fav-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container mt-5">
    <h2>Upload File</h2>

    <?php if (isset($_SESSION["error"])) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION["error"]) ?></div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION["success"])) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION["success"]) ?></div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    <br>
    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
