<?php
// Include database connection
include('db_connect.php');
include('auth.php');


$userID = $_SESSION['customerID'];
$totalAmount = $_SESSION['total'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$address = $latitude . ", " . $longitude;

// Step 1: Create Order
$sql_order = "INSERT INTO orders (customerID, totalPrice,address) 
              VALUES (?, ?, ?)";
$stmt = $connection->prepare($sql_order);
$stmt->bind_param("ids", $userID, $totalAmount,$address );
$stmt->execute();
$orderID = $stmt->insert_id; // Get the last inserted order ID
$stmt->close();


// Payment details from form
$amount = $_SESSION['total'];  // The total from session
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

// Optionally, update the order status to 'completed' (if payment successful)
$sql_update_order = "UPDATE orders SET status = 'Shipped' WHERE orderID = ?";
$stmt = $connection->prepare($sql_update_order);
$stmt->bind_param("i", $orderID);
$stmt->execute();
$stmt->close();

header("Location: homebage2.php"); // Redirect to the confirmation page
exit();

?>
