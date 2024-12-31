<?php
// Start session
session_start();

// Destroy session to log out the user
session_destroy();

// Redirect to login page
header('Location: userlogin.php');
exit();
?>
