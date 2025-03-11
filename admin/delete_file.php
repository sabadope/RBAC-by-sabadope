<?php
include '../src/config.php';
include 'admin_auth.php';

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    // Fetch the file details
    $query = "SELECT file_name FROM uploads WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $file_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $file_name);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($file_name) {
        // Delete from database
        $delete_query = "DELETE FROM uploads WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $file_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from server
        unlink("../uploads/" . $file_name);

        header("Location: view_upload.php?success=File deleted");
        exit;
    }
}
?>
