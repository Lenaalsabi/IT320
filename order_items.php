<?php
include 'auth.php';
include 'db_connect.php';

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

        $stmtBookUpdate->close();
    } else {
        echo "Error inserting order item: " . $stmtOrderItem->error;
    }
}

if (isset($_POST['checkout'])) {
    $customerID = $_SESSION['customerID'];

    // Retrieve cart items data with price from Book table
    $sqlCartItems = "SELECT cart_items.ISBN, cart_items.quantity, book.price, cartID FROM cart_items JOIN book ON cart_items.ISBN = book.ISBN WHERE cartID = (SELECT cartID FROM cart WHERE customerID = ?)";
    $stmtCartItems = $connection->prepare($sqlCartItems);
    $stmtCartItems->bind_param("i", $customerID);
    $stmtCartItems->execute();
    $resultCartItems = $stmtCartItems->get_result();

    if ($resultCartItems->num_rows > 0) {
        $_SESSION['order_data'] = [];
        $cartIDToDelete = null; // Store the cartID for deletion

        while ($row = $resultCartItems->fetch_assoc()) {
            $_SESSION['order_data'][] = [
                'ISBN' => $row['ISBN'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'startDate' => null,
                'endDate' => null,
                'type' => 'Purchase',
                'status' => null // Set initial status for purchase
            ];
            $cartIDToDelete = $row['cartID']; // Capture cartID, assume all cart items have same cartID for the user.
        }

        echo "Session ID (order_items.php): " . session_id() . "<br>";

        // Delete items from cart_items table
        if ($cartIDToDelete !== null) {
            $sqlDeleteCartItems = "DELETE FROM cart_items WHERE cartID = ?"; // Corrected line
            $stmtDeleteCartItems = $connection->prepare($sqlDeleteCartItems);
            $stmtDeleteCartItems->bind_param("i", $cartIDToDelete);
            $stmtDeleteCartItems->execute();
            $stmtDeleteCartItems->close();
        }

        header("Location: checkout.php"); // Corrected redirection
        exit;
    } else {
        echo "Your cart is empty.";
    }
}

if (isset($_POST['booking'])) {
    echo '<p> working </p>';
    // Borrowing Logic
    $isbn = $_POST['ISBN'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];
    $title = $_POST['book-title'];

    // 1. Calculate Total Price (Server-Side!)
    $startDateTime = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    $interval = $startDateTime->diff($endDateTime);
    $daysDifference = $interval->days;
    $pricePerDay = 2;
    $totalPrice = $daysDifference * $pricePerDay;

    // 2. Store borrowing order data in session
    $_SESSION['order_data'][] = [
        'ISBN' => $isbn,
        'quantity' => 1,
        'price' => $totalPrice,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'type' => 'Borrow',
        'status' => 'Borrowed' // Set initial status for borrow
    ];

    $_SESSION['total_price'] = $totalPrice;// Debugging: Check the session
    echo "<pre>";
    print_r($_SESSION['order_data']);
    echo "</pre>";

    echo "Session ID (order_items.php): " . session_id() . "<br>";

    header("Location: checkout.php");
    exit();
}

?>