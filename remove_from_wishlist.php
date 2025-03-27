<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['customerID'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$customerID = $_SESSION['customerID'];
$ISBN = $_POST['ISBN'];

$query = "DELETE FROM wishlist WHERE customerID = ? AND ISBN = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("is", $customerID, $ISBN);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "The book has been removed from the wishlist "]);
} else {
    echo json_encode(["status" => "error", "message" => "An error occurred"]);
}

$stmt->close();
$connection->close();
?>
