<?php
//var_dump($_SESSION);
include('auth.php');
include('db_connect.php');



if (isset($_POST['pays'])) {
// Retrieve session-stored order data
    $orderData = $_SESSION['order_data'];
    if (!$orderData) {
        die("Error: Order data is missing in session.");
    }


    //

    $userID = $_SESSION['customerID'];
//$totalAmount = $_SESSION['total_price'];
    $totalPrice = $_SESSION['total_price']; // Retrieve from session

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $address = $latitude . ", " . $longitude;

// Step 1: Create Order
    $sql_order = "INSERT INTO orders (customerID, totalPrice,address) 
              VALUES (?, ?, ?)";
    $stmt = $connection->prepare($sql_order);
    $stmt->bind_param("ids", $userID, $totalPrice,$address );
    $stmt->execute();
    $orderID = $stmt->insert_id; // Get the last inserted order ID
    $stmt->close();


// Payment details from form
    $amount =$_SESSION['total_price']; // The total from session
    $card_number = $_POST['card-number']; //
    $expiry = $_POST['expiry']; // Expiry date (YY-MM-DD)
    $cvv = $_POST['cvv']; // CVV

    $digit4=substr($card_number, -4);


// Step 2: Process Payment
    $sql_payment = "INSERT INTO payments (orderID, amount, last4Digits, expiryDate, cvv) 
                VALUES (?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql_payment);
    $stmt->bind_param("dssss", $orderID, $amount, $digit4, $expiry, $cvv);
    $stmt->execute();
    $paymentID = $stmt->insert_id; // Get the last inserted payment ID
    $stmt->close();

    /*Optionally, update the order status to 'completed' (if payment successful)
    $sql_update_order = "UPDATE orders SET status = 'Shipped' WHERE orderID = ?";
    $stmt = $connection->prepare($sql_update_order);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();
    /*
    header("Location: homebage2.php"); // Redirect to the confirmation page
    exit();
    */

// Clear the total price from the session after processing (optional)
//unset($_SESSION['total_price']);


    // Step 3: Insert Order Items
    foreach ($orderData as $item) {
        echo "Processing item: ISBN=" . $item['ISBN'] . ", Quantity=" . $item['quantity'] . ", Price=" . $item['price'] . "<br>";

        $sqlOrderItem = "INSERT INTO order_items (orderID, ISBN, type, quantity, startDate, endDate, totalPrice, status) 
                         VALUES (?, ?, 'Purchase', ?, NULL, NULL, ?, NULL)";
        $stmtOrderItem = $connection->prepare($sqlOrderItem);

        if (!$stmtOrderItem) {
            die("Error preparing query: " . $connection->error);
        }

        $stmtOrderItem->bind_param("isid", $orderID, $item['ISBN'], $item['quantity'], $item['price']);
        $stmtOrderItem->execute();

        if ($stmtOrderItem->errno) {
            echo "Error inserting order item: " . $stmtOrderItem->error . "<br>";
        } else {
            echo "Inserted order item successfully!<br>";
        }

        $stmtOrderItem->close();
    }

    // Clear session data after processing
    unset($_SESSION['total_price']);

}


header("Location: homebage2.php"); // Redirect to the confirmation page
exit();
?>