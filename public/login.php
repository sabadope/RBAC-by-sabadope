<?php
session_start();
include '../src/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["name"] = $name;
        $_SESSION["role"] = $role;

        header("Location: dashboard.php");
    } else {
        echo "Invalid credentials.";
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
      <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "MS Sans Serif", sans-serif; }
        body { display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100vh; background: #008080; position: relative; overflow: hidden; }
        .desktop-icon { position: absolute; top: 20px; left: 20px; cursor: pointer; text-align: center; color: white; }
        .desktop-icon img { width: 50px; height: 50px; }
        .container { width: 350px; padding: 20px; background: #C0C0C0; border: 2px solid black; box-shadow: 4px 4px black; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none; }
        .title-bar { background: navy; color: white; padding: 5px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .close-btn { background: red; color: white; border: none; width: 20px; height: 20px; text-align: center; cursor: pointer; font-weight: bold; }
        .form { padding: 10px; border: 2px inset black; background: #E0E0E0; }
        h2 { margin-bottom: 15px; font-size: 14px; }
        input, button { width: 100%; padding: 5px; margin: 10px 0; border: 2px inset black; background: white; }
        button { background: #C0C0C0; border: 2px outset black; cursor: pointer; }
        button:active { border: 2px inset black; }
        .toggle-link { margin-top: 10px; display: block; color: black; cursor: pointer; text-decoration: underline; }
        .taskbar { width: 100%; height: 30px; background: #C0C0C0; border-top: 2px solid black; display: flex; align-items: center; padding: 5px; position: absolute; bottom: 0; left: 0; }
        .taskbar .task { padding: 5px; background: #E0E0E0; border: 1px solid black; margin-right: 5px; cursor: pointer; }
    </style>
  </style>
</head>
<body>

    <div class="desktop-icon" onclick="toggleLogin()">
        <img src="icon.png" alt="Login Icon">
        <p>Login</p>
    </div>

    <div class="container" id="loginWindow">
        <div class="title-bar">
            Login
            <button class="close-btn" onclick="toggleLogin()">X</button>
        </div>
        <div class="form">
            <h2>Enter your credentials</h2>
            <form method="POST">
                <input type="email" name="email" required placeholder="Email">
                <input type="password" name="password" required placeholder="Password">
                <button type="submit">Login</button>
            </form>
            <a href="register.php" class="toggle-link">Don’t have an account? Register here</a>
        </div>
        <a href="../admin/admin_login.php?force_auth=1">Admin Login</a>
    </div>        
    <div class="taskbar">
        <div class="task" onclick="toggleLogin()">Login</div>
    </div>
    <script>
        function toggleLogin() {
            let loginWindow = document.getElementById("loginWindow");
            loginWindow.style.display = (loginWindow.style.display === "none" || loginWindow.style.display === "") ? "block" : "none";
        }
    </script>
</body>
</html>
