<?php
session_start(); // Ensure session starts on every page

if (!isset($_SESSION['customerID'])) {
    header("Location: homepage.html"); // Redirect to homepage if not logged in
    exit();
}
?>