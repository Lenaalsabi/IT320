<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderID = intval($_POST['orderID']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $address = $_POST['address'];

    // تحديث الطلب في order_items
    $sql_update_items = "UPDATE order_items SET startDate = ?, endDate = ? WHERE orderID = ? AND type = 'Borrow'";
    $stmt_update_items = $connection->prepare($sql_update_items);
    $stmt_update_items->bind_param("ssi", $startDate, $endDate, $orderID);
    
    // تحديث العنوان في orders
    $sql_update_orders = "UPDATE orders SET address = ? WHERE orderID = ?";
    $stmt_update_orders = $connection->prepare($sql_update_orders);
    $stmt_update_orders->bind_param("si", $address, $orderID);

    if ($stmt_update_items->execute() && $stmt_update_orders->execute()) {
        echo "<script>alert('Reservation updated successfully!'); window.location.href='orders.php';</script>";
    } else {
        echo "<script>alert('Error updating reservation.'); window.history.back();</script>";
    }
}
?>
