<?php
include '../src/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: login.php?registered=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RBAC Windows 95</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/img/fav-logo.png">
  <!-- Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "MS Sans Serif", sans-serif; }
        body { display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100vh; background: #008080; position: relative; overflow: hidden; }
        .desktop-icon { position: absolute; top: 20px; left: 20px; cursor: pointer; text-align: center; color: white; }
        .desktop-icon img { width: 50px; height: 50px; }
        .container { width: 350px; padding: 20px; background: #C0C0C0; border: 2px solid black; box-shadow: 4px 4px black; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none; }
        .title-bar { background: navy; color: white; padding: 5px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .close-btn { background: red; color: white; border: none; width: 20px; height: 20px; text-align: center; cursor: pointer; font-weight: bold; }
        .form { display: none; padding: 10px; border: 2px inset black; background: #E0E0E0; }
        .form.active { display: block; }
        h2 { margin-bottom: 15px; font-size: 14px; }
        input, select { width: 100%; padding: 5px; margin: 10px 0; border: 2px inset black; background: white; }
        button { width: 100%; padding: 5px; background: #C0C0C0; border: 2px outset black; cursor: pointer; }
        button:active { border: 2px inset black; }
        .toggle-link { margin-top: 10px; display: block; color: black; cursor: pointer; text-decoration: underline; }
        .taskbar { width: 100%; height: 30px; background: #C0C0C0; border-top: 2px solid black; display: flex; align-items: center; padding: 5px; position: absolute; bottom: 0; left: 0; }
        .taskbar .task { padding: 5px; background: #E0E0E0; border: 1px solid black; margin-right: 5px; cursor: pointer; }
  </style>
</head>
<body>

    <!-- Desktop Icon -->
    <div class="desktop-icon" onclick="openWindow()">
        <img src="icon.png" alt="Register">
        <div>Register</div>
    </div>

    <!-- Register Window -->
    <div class="container" id="registerWindow">
        <div class="title-bar">
            Register
            <button class="close-btn" onclick="closeWindow()">X</button>
        </div>
        <div class="form active">
            <h2>Create an Account</h2>
            <?php if (isset($_SESSION["error"])) { echo "<p style='color: red;'>".$_SESSION["error"]."</p>"; unset($_SESSION["error"]); } ?>
            <form method="POST">
                <input type="text" name="name" required placeholder="Full Name">
                <input type="email" name="email" required placeholder="Email">
                <input type="password" name="password" required placeholder="Password">
                <select name="role">
                    <option value="User">User</option>
                    <option value="Manager">Manager</option>
                </select>
                <button type="submit">Register</button>
            </form>
            <a href="login.php" class="toggle-link">Already have an account? Login here</a>
        </div>
    </div>

    <!-- Taskbar -->
    <div class="taskbar">
        <div class="task" id="taskRegister" onclick="openWindow()">Register</div>
    </div>

    <script>
        function openWindow() {
            document.getElementById("registerWindow").style.display = "block";
            document.getElementById("taskRegister").style.display = "block";
        }
        function closeWindow() {
            document.getElementById("registerWindow").style.display = "none";
        }
    </script>
</body>
</html>
