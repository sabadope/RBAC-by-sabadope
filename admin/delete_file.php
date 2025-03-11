<?php
session_start();
include '../src/config.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if file ID is provided
if (isset($_GET['id'])) {
    $file_id = intval($_GET['id']);

    // Fetch file details from database
    $stmt = $conn->prepare("SELECT id, filename, filepath FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();
    $stmt->close();

    if ($file) {
        $filePath = $file['filepath'];

        // Delete file from server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete record from database
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "✅ File deleted successfully.";
    } else {
        $_SESSION['message'] = "❌ File not found.";
    }
} else {
    $_SESSION['message'] = "❌ Invalid request.";
}

// Redirect back to view files
header("Location: view_files.php");
exit();
?>
