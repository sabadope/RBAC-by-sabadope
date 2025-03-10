<?php
session_start();
require '../config/db.php'; // Include database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Check if file ID is provided
if (isset($_GET['id'])) {
    $file_id = intval($_GET['id']);

    // Get the file details from the database
    $query = "SELECT file_name FROM uploads WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $file_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $file = mysqli_fetch_assoc($result);

    if ($file) {
        $file_path = "../uploads/" . $file['file_name'];

        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete the record from the database
        $delete_query = "DELETE FROM uploads WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $file_id);
        mysqli_stmt_execute($delete_stmt);

        // Redirect back to view_files.php
        header("Location: view_files.php?success=File deleted successfully");
        exit;
    } else {
        header("Location: view_files.php?error=File not found");
        exit;
    }
} else {
    header("Location: view_files.php?error=Invalid request");
    exit;
}
?>
