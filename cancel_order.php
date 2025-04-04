<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

session_start();
if (!isset($_SESSION['customerID'])) {
    header("Location: homepage.html"); 
    exit();
}

$customerID = $_SESSION['customerID'];

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["orderID"])) {
    $orderID = intval($_POST["orderID"]); 

    $checkQuery = "SELECT status FROM orders WHERE orderID = ? AND customerID = ?";
    $stmt = $connection->prepare($checkQuery);
    if (!$stmt) {
        die("Query preparation failed: " . $connection->error);
    }

    $stmt->bind_param("ii", $orderID, $customerID);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($status);
        $stmt->fetch();
        
        if ($status == 'Pending') {
            $updateQuery = "UPDATE orders SET status = 'Cancelled' WHERE orderID = ?";
            $stmt = $connection->prepare($updateQuery);
            if (!$stmt) {
                die("Update query preparation failed: " . $connection->error);
            }

            $stmt->bind_param("i", $orderID);
            if ($stmt->execute()) {
                echo "success";
            } else {
                die("Error updating order status: " . $stmt->error);
            }
        } else {
            echo "Order not in 'Pending' status"; 
        }
    } else {
        echo "Order not found"; 
    }
}
?>
