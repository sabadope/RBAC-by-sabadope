<?php
session_start();
session_unset();
session_destroy();

// Force browser to forget authentication credentials
header('HTTP/1.0 401 Unauthorized');
header('Location: ../public/login.php');
exit;
?>
