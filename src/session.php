<?php
session_start();

// Security measures
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable if using HTTPS

// Session timeout (15 min)
$timeout = 900; // 900 seconds = 15 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: ../public/login.php?session_expired=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Redirect if user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../public/login.php");
    exit();
}

// Function to check roles
function checkRole($allowed_roles) {
    if (!in_array($_SESSION["role"], $allowed_roles)) {
        header("Location: ../public/dashboard.php");
        exit();
    }
}
?>
