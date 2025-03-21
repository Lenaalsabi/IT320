<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to homepage or login page
header("Location: ../homepage.html");
exit();
?>
