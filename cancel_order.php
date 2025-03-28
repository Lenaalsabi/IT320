<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["orderID"])) {
    session_start();

    if (!isset($_SESSION['customerID'])) {
        echo "Customer ID is not set in the session.";
        exit();
    }

    $customerID = $_SESSION['customerID'];

    if (!$connection) {
        echo "Database connection failed: " . mysqli_connect_error();
        exit();
    }

    $orderID = intval($_POST["orderID"]); 

    $checkQuery = "SELECT status FROM orders WHERE orderID = ? AND customerID = ?";
    $stmt = $connection->prepare($checkQuery);
    $stmt->bind_param("ii", $orderID, $customerID);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($status);
        $stmt->fetch();
        
        if ($status == 'Pending') {
            $updateQuery = "UPDATE orders SET status = 'Cancelled' WHERE orderID = ?";
            $stmt = $connection->prepare($updateQuery);
            $stmt->bind_param("i", $orderID);
            
            if ($stmt->execute()) {
                echo "Order status updated to 'Cancelled'.";
            } else {
                echo "Error updating order status: " . $stmt->error;
            }
            
        } else {
            echo "Order not in 'Pending' status"; 
        }
    } else {
        echo "Order not found"; 
    }
}
?>
