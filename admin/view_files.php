<?php
session_start();
require '../config/db.php'; // Include database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch all uploaded files from the database
$query = "SELECT * FROM uploads ORDER BY upload_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Files</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link to your CSS -->
</head>
<body>

<h2>Uploaded Files</h2>

<table border="1">
    <tr>
        <th>File Name</th>
        <th>Uploader</th>
        <th>Upload Date</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo htmlspecialchars($row['file_name']); ?></td>
        <td><?php echo htmlspecialchars($row['uploaded_by']); ?></td>
        <td><?php echo htmlspecialchars($row['upload_date']); ?></td>
        <td>
            <a href="../uploads/<?php echo $row['file_name']; ?>" target="_blank">View</a> |
            <a href="../uploads/<?php echo $row['file_name']; ?>" download>Download</a> |
            <a href="delete_file.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this file?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
