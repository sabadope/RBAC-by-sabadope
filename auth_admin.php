<?php
echo "Enter Admin Password: ";
$handle = fopen ("php://stdin","r");
$line = trim(fgets($handle));

if ($line === "your_admin_password") {
    echo "Access Granted!\n";
    exec("start http://localhost/rbac_project/admin_dashboard.php");
} else {
    echo "Access Denied!\n";
}
?>