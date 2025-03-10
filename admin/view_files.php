<?php
session_start();
include '../src/config.php';

// Ensure only admin can access
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["role"] !== "Admin") {
    header("Location: admin_dashboard.php");
    exit();
}

// Admin ID (Make sure this matches the admin's stored user_id)
$admin_id = 1; // Change if needed to match the admin's actual ID

// Fetch files uploaded by users & admin
$query = "SELECT 
            files.id, 
            files.filename, 
            files.filepath, 
            CASE 
                WHEN files.user_id = ? THEN 'Admin' 
                ELSE users.name 
            END AS uploader 
          FROM files 
          LEFT JOIN users ON files.user_id = users.id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Uploaded Files</title>
    <link rel="icon" type="image/png" href="../assets/img/fav-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container mt-4">
    <h2>All Uploaded Files</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Uploaded By</th>
                    <th>File Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["uploader"] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($row["filename"]); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($row["filepath"]); ?>" download class="btn btn-primary">Download</a>
                            <a href="delete_file.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this file?')" 
                               class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning">No files have been uploaded yet.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>