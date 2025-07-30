<?php
// session_start();

// Set admin session for testing
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = 'admin';

echo "Admin session created successfully!\n";
echo "You can now test the chart saving functionality.\n";
?>
