<?php
include 'db_connect.php';
include 'auth.php';
var_dump($_POST);


$userId = $_SESSION['customerID'];
$isbn = $_POST['isbn'];
$quantity = $_POST['quantity'];

// Get the cartID of the current user
$sqlGetCart = "SELECT cartID FROM Cart WHERE customerID = ?";
$stmtGetCart = $connection->prepare($sqlGetCart);
$stmtGetCart->bind_param("i", $userId);
$stmtGetCart->execute();
$resultGetCart = $stmtGetCart->get_result();

if ($resultGetCart->num_rows > 0) {
    $rowCart = $resultGetCart->fetch_assoc();
    $cartId = $rowCart['cartID'];

    // Check if the item already exists in the cart
    $sqlCheck = "SELECT * FROM cart_items WHERE cartID = ? AND ISBN = ?";
    $stmtCheck = $connection->prepare($sqlCheck);
    $stmtCheck->bind_param("is", $cartId, $isbn);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Item exists, update quantity
        $sqlUpdate = "UPDATE cart_items SET quantity = quantity + ? WHERE cartID = ? AND ISBN = ?";
        $stmtUpdate = $connection->prepare($sqlUpdate);
        $stmtUpdate->bind_param("iis", $quantity, $cartId, $isbn);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } else {
        // Item doesn't exist, insert new item
        $sqlInsert = "INSERT INTO cart_items (cartID, ISBN, quantity) VALUES (?, ?, ?)";
        $stmtInsert = $connection->prepare($sqlInsert);
        $stmtInsert->bind_param("isi", $cartId, $isbn, $quantity);
        $stmtInsert->execute();
        $stmtInsert->close();
    }


    echo "Item added to cart";

} else {
    echo "Cart not found";
}

$stmtGetCart->close();
$connection->close();
?>