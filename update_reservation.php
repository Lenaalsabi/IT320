<?php
require 'db_connect.php';
include 'auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderID = intval($_POST['orderID']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $address = $_POST['address'];

    $today = date('Y-m-d');
    if ($startDate < $today || $endDate < $today) {
        echo "<script>alert('Dates must be in the future.'); window.history.back();</script>";
        exit();
    }

    // Get current statuses and ISBN
    $sql = "SELECT oi.ISBN, oi.status AS itemStatus, o.status AS orderStatus, o.address, oi.startDate, oi.endDate 
            FROM order_items oi 
            JOIN orders o ON oi.orderID = o.orderID 
            WHERE oi.orderID = ? AND oi.type = 'Borrow'";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Reservation not found.'); window.history.back();</script>";
        exit();
    }

    $row = $result->fetch_assoc();
    $isbn = $row['ISBN'];
    $itemStatus = $row['itemStatus'];
    $orderStatus = $row['orderStatus'];
    $currentAddress = $row['address'];
    $currentStart = $row['startDate'];
    $currentEnd = $row['endDate'];

    // Check for date conflicts
    $conflict_sql = "SELECT * FROM order_items oi 
                     JOIN orders o ON oi.orderID = o.orderID 
                     WHERE oi.ISBN = ? AND oi.orderID != ? 
                     AND oi.type = 'Borrow' 
                     AND o.status != 'Cancelled'
                     AND (
                         (oi.startDate BETWEEN ? AND ?) OR
                         (oi.endDate BETWEEN ? AND ?) OR
                         (? BETWEEN oi.startDate AND oi.endDate) OR
                         (? BETWEEN oi.startDate AND oi.endDate)
                     )";
    $stmt_conflict = $connection->prepare($conflict_sql);
    $stmt_conflict->bind_param("sissssss", $isbn, $orderID, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
    $stmt_conflict->execute();
    $conflict_result = $stmt_conflict->get_result();

    if ($conflict_result->num_rows > 0) {
        echo "<script>alert('Date conflict with another reservation.'); window.history.back();</script>";
        exit();
    }

    $somethingUpdated = false;
    $success = true;

    // Update order_items if allowed
    if (($itemStatus !== 'returned' && $endDate != $currentEnd) || ($orderStatus === 'Pending' && $startDate != $currentStart)) {
        $updates = [];
        $types = "";
        $params = [];

        if ($itemStatus !== 'returned' && $endDate != $currentEnd) {
            $updates[] = "endDate = ?";
            $types .= "s";
            $params[] = $endDate;

            // Also update totalPrice
            $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
            $totalPrice = $days * 2;

            $updates[] = "totalPrice = ?";
            $types .= "d";
            $params[] = $totalPrice;
        }

        if ($orderStatus === 'Pending' && $startDate != $currentStart) {
            $updates[] = "startDate = ?";
            $types .= "s";
            $params[] = $startDate;
        }

        $update_items_sql = "UPDATE order_items SET " . implode(", ", $updates) . " WHERE orderID = ? AND type = 'Borrow'";
        $types .= "i";
        $params[] = $orderID;

        $stmt_update = $connection->prepare($update_items_sql);
        $stmt_update->bind_param($types, ...$params);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            $somethingUpdated = true;
        }
    }

    // Update address if allowed and changed
    if ($orderStatus !== 'Shipped' && $address !== $currentAddress) {
        $stmt_address = $connection->prepare("UPDATE orders SET address = ? WHERE orderID = ?");
        $stmt_address->bind_param("si", $address, $orderID);
        $stmt_address->execute();

        if ($stmt_address->affected_rows > 0) {
            $somethingUpdated = true;
        }
    }

    // Show appropriate message
    if ($somethingUpdated) {
        echo "<script>alert('Reservation updated successfully.'); window.location.href='orders.php';</script>";
    } else {
        echo "<script>alert('No changes were made. Make sure your edits are allowed.'); window.location.href='orders.php';</script>";
    }
}
?>
