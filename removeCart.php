
<?php


include 'db_connect.php'; // Make sure this is the correct path

if (isset($_POST['cartId']) && isset($_POST['isbn'])) {
    $cartId = $_POST['cartId'];
    $isbn = $_POST['isbn'];

    $isbn = mysqli_real_escape_string($connection, $isbn);

    $stmt = $connection->prepare("DELETE FROM cart_items WHERE cartID = ? AND ISBN = ?");
    $stmt->bind_param("is", $cartId, $isbn);

    if ($stmt->execute()) {
        //echo "success";
    } else {
        echo "error, " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
    header("Location:cart.php");
} else {
    echo "invalid request";
}

?>





