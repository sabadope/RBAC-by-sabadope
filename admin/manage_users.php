<?php
session_start();
require_once '../src/config.php';
require_once '../src/auth.php';

// Ensure only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Location: ../public/login.php');
    exit();
}

// Fetch all users except Admin
$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE role != 'Admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];
    
    $updateStmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $updateStmt->execute([$newRole, $userId]);
    
    header("Location: manage_users.php");
    exit();
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$userId]);
    
    header("Location: manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Manage Users</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="new_role">
                            <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
                            <option value="Manager" <?= $user['role'] === 'Manager' ? 'selected' : '' ?>>Manager</option>
                        </select>
                        <button type="submit" name="update_role">Update</button>
                    </form>
                </td>
                <td>
                    <form method="post" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="delete_user">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
