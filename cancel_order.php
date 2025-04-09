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
            $stmtUpdate = $connection->prepare($updateQuery);
            if (!$stmtUpdate) {
                die("Update query preparation failed: " . $connection->error);
            }

            $stmtUpdate->bind_param("i", $orderID);
            if ($stmtUpdate->execute()) {
                
                // ✅ نرجّع الكمية للمخزون
                $selectItemsQuery = "SELECT ISBN, quantity FROM order_items WHERE orderID = ?";
                $stmtItems = $connection->prepare($selectItemsQuery);
                $stmtItems->bind_param("i", $orderID);
                $stmtItems->execute();
                $result = $stmtItems->get_result();

                while ($row = $result->fetch_assoc()) {
                    $isbn = $row['ISBN'];
                    $quantity = $row['quantity'];

                    $updateStockQuery = "UPDATE book SET stock_quantity = stock_quantity + ? WHERE ISBN = ?";
                    $stmtUpdateStock = $connection->prepare($updateStockQuery);
                    $stmtUpdateStock->bind_param("is", $quantity, $isbn);
                    $stmtUpdateStock->execute();
                }

                echo "success";
            } else {
                die("Error updating order status: " . $stmtUpdate->error);
            }
        } else {
            echo "Order not in 'Pending' status"; 
        }
    } else {
        echo "Order not found"; 
    }
}
?>
