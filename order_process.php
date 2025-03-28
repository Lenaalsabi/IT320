<?php

// Include database connection
include('db_connect.php');
include('auth.php');

/*Start session to get user ID and other info
session_start();
*/
// Assuming userID, productID, totalAmount, latitude, longitude come from session or form input
$userID = $_SESSION['customerID'];  // Replace with actual user session ID
$totalAmount = 55; // Total amount from cart
$latitude = $_POST['latitude']; // Latitude from the map
$longitude = $_POST['longitude']; // Longitude from the map

$address = $latitude . ", " . $longitude;

// Step 1: Create Order
$sql_order = "INSERT INTO orders (customerID, totalPrice,address) 
              VALUES (?, ?, ?)";
$stmt = $connection->prepare($sql_order);
$stmt->bind_param("ids", $userID, $totalAmount, $address);
$stmt->execute();
$orderID = $stmt->insert_id; // Get the last inserted order ID
$stmt->close();

// Now, let's process the payment

// Payment details from form
$amount = 67; // Payment amount
$card_number = $_POST['card-number']; //
$expiry = $_POST['expiry']; // Expiry date (YY-MM-DD)
$cvv = $_POST['cvv']; // CVV

$digit4 = substr($card_number, -4);


// Step 2: Process Payment
$sql_payment = "INSERT INTO payments (orderID, amount, last4Digits, expiryDate, cvv) 
                VALUES (?, ?, ?, ?, ?)";
$stmt = $connection->prepare($sql_payment);
$stmt->bind_param("dssss", $orderID, $amount, $digit4, $expiry, $cvv);
$stmt->execute();
$paymentID = $stmt->insert_id; // Get the last inserted payment ID
$stmt->close();

// Optionally, update the order status to 'completed' (if payment successful)
$sql_update_order = "UPDATE orders SET status = 'Shipped' WHERE orderID = ?";
$stmt = $connection->prepare($sql_update_order);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$stmt->close();

header("Location: homebage2.php"); // Redirect to the confirmation page
exit();

?>