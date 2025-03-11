<?php
session_start();
include '../src/config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"];
$user_id = $_SESSION["user_id"];

// ✅ Secure Query to Fetch Files Based on Role
$query = "SELECT users.id AS user_id, users.name, users.role, files.id AS file_id, files.filename, files.filepath 
          FROM files 
          JOIN users ON files.user_id = users.id";

if ($role === "User") {
    $query .= " WHERE users.id = ?";
} elseif ($role === "Manager") {
    $query .= " WHERE users.role IN ('User', 'Manager')";
} elseif ($role === "Admin") {
    $query .= " WHERE users.role IN ('User', 'Manager', 'Admin') OR users.role = 'Admin'";
}

$stmt = $conn->prepare($query);

if ($role === "User") {
    $stmt->bind_param("i", $user_id);
}

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

    <!-- Show success or error messages -->
    <?php if (isset($_SESSION["success"])): ?>
        <div class="alert alert-success"><?php echo $_SESSION["success"]; unset($_SESSION["success"]); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION["error"])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Uploaded By</th>
                <th>Role</th>
                <th>File Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <td><?php echo htmlspecialchars($row["role"]); ?></td>
                    <td><?php echo htmlspecialchars($row["filename"]); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($row["filepath"]); ?>" download class="btn btn-primary">Download</a>
                        
                        <?php if ($role === "Admin"): // Admins can delete any file ?>
                            <a href="delete_file.php?id=<?php echo $row['file_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this file?')" 
                               class="btn btn-danger">Delete</a>
                        <?php elseif ($row["user_id"] == $user_id): // Users & Managers can delete only their own files ?>
                            <a href="delete_file.php?id=<?php echo $row['file_id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this file?')" 
                               class="btn btn-warning">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

</body>
</html>
