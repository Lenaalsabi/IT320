<?php
session_start();
header('Content-Type: application/json'); 
include 'db_connect.php';

if (!isset($_SESSION['customerID'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

if (!isset($_POST['ISBN']) || empty($_POST['ISBN'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request. ISBN is missing."]);
    exit;
}

$customerID = $_SESSION['customerID'];
$ISBN = $_POST['ISBN'];

$checkQuery = "SELECT 1 FROM wishlist WHERE customerID = ? AND ISBN = ?";
$checkStmt = $connection->prepare($checkQuery);
$checkStmt->bind_param("is", $customerID, $ISBN);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "The book is already in the wishlist."]);
} else {
    $query = "INSERT INTO wishlist (customerID, ISBN) VALUES (?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("is", $customerID, $ISBN);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Added to the wishlist"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add the book to wishlist."]);
    }

    $stmt->close();
}

$checkStmt->close();
$connection->close();
?>
