<?php
include 'auth.php';
include 'db_connect.php';

if (isset($_POST['checkout'])) {
    $customerID = $_SESSION['customerID'];

    // Retrieve cart items data with price from Book table
    $sqlCartItems = "SELECT cart_items.ISBN, cart_items.quantity, book.price FROM cart_items JOIN book ON cart_items.ISBN = book.ISBN WHERE cartID = (SELECT cartID FROM cart WHERE customerID = ?)";
    $stmtCartItems = $connection->prepare($sqlCartItems);
    $stmtCartItems->bind_param("i", $customerID);
    $stmtCartItems->execute();
    $resultCartItems = $stmtCartItems->get_result();

    if ($resultCartItems->num_rows > 0) {
        $_SESSION['order_data'] = [];
        while ($row = $resultCartItems->fetch_assoc()) {
            $_SESSION['order_data'][] = [
                'ISBN' => $row['ISBN'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
        }

        echo "Session ID (order_items.php): " . session_id() . "<br>";

        header("Location: checkout.php"); // Corrected redirection
        exit;
    } else {
        echo "Your cart is empty.";
    }
}
?>