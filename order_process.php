<?php
//var_dump($_SESSION);
include('auth.php');
include('db_connect.php');


// Function to insert order item and update book stock (as defined previously)
function insertOrderItemAndUpdateStock($connection, $orderID, $item) {
    $sqlOrderItem = "INSERT INTO order_items (orderID, ISBN, type, quantity, startDate, endDate, totalPrice, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtOrderItem = $connection->prepare($sqlOrderItem);

    if (!$stmtOrderItem) {
        die("Error preparing order item query: " . $connection->error);
    }

    $type = $item['type'];
    $startDate = $item['startDate'];
    $endDate = $item['endDate'];
    $status = $item['status'];

    $stmtOrderItem->bind_param("isssssds", $orderID, $item['ISBN'], $type, $item['quantity'], $startDate, $endDate, $item['price'], $status);

    if ($stmtOrderItem->execute()) {
        $stmtOrderItem->close();

        if($type=='Purchase'){
            // Update book stock

            $sqlBookUpdate = "UPDATE book SET stock_quantity = stock_quantity - ? WHERE ISBN = ?";
            $stmtBookUpdate = $connection->prepare($sqlBookUpdate);

            if (!$stmtBookUpdate) {
                die("Error preparing book update query: " . $connection->error);
            }

            $stmtBookUpdate->bind_param("is", $item['quantity'], $item['ISBN']);

            if (!$stmtBookUpdate->execute()) {
                echo "Error updating book stock: " . $stmtBookUpdate->error;
            }

            $stmtBookUpdate->close();}
    } else {
        echo "Error inserting order item: " . $stmtOrderItem->error;
    }
}





if (isset($_POST['pays'])) {
// Retrieve session-stored order data
    if (isset($_SESSION['order_data'])) {
        $orderData = $_SESSION['order_data'];
        $userID = $_SESSION['customerID'];
        $totalPrice = $_SESSION['total_price'];


        //

        $userID = $_SESSION['customerID'];
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


// Clear the total price from the session after processing (optional)
//unset($_SESSION['total_price']);


        /* 3. Insert Order Items
      foreach ($orderData as $item) {
            $sqlOrderItem = "INSERT INTO order_items (orderID, ISBN, type, quantity, startDate, endDate, totalPrice, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtOrderItem = $connection->prepare($sqlOrderItem);
            if (!$stmtOrderItem) {
                die("Error preparing query: " . $connection->error);
            }
            $type = $item['type'];
            $startDate = $item['startDate'];
            $endDate = $item['endDate'];
            $status=$item['status'];
            $stmtOrderItem->bind_param("isssssds", $orderID, $item['ISBN'], $type, $item['quantity'], $startDate, $endDate, $item['price'],$status);
            $stmtOrderItem->execute();
            $stmtOrderItem->close();
          //  if($type=='Purchase')
             insertOrderItemAndUpdateStock($connection, $orderID, $item);

       // }*/

        // 2. Insert into 'order_items' and update book stock
        foreach ($orderData as $item) {
            insertOrderItemAndUpdateStock($connection, $orderID, $item);
        }

        // Clear session data
        unset($_SESSION['order_data']);
        unset($_SESSION['total_price']);
        header("Location: homebage2.php"); // Redirect to the confirmation page
        exit();

    } else {
        die("Error: Order data is missing in session.");
    }
}
?>