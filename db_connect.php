<?php
$connection = mysqli_connect("localhost", "root", "root", "Mawj","8889");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

?>