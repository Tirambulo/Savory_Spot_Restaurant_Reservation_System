<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login.php file (which is in the same directory: handlers/)
header('Location: login.php'); 
exit;
?>