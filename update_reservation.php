<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderID = intval($_POST['orderID']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $address = $_POST['address'];

    // احضار رقم ISBN للكتاب المرتبط بالطلب
    $sql_get_isbn = "SELECT ISBN FROM order_items WHERE orderID = ? AND type = 'Borrow'";
    $stmt_get_isbn = $connection->prepare($sql_get_isbn);
    $stmt_get_isbn->bind_param("i", $orderID);
    $stmt_get_isbn->execute();
    $result = $stmt_get_isbn->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $isbn = $row['ISBN'];

        // التحقق من التداخل مع الحجوزات الأخرى لنفس الكتاب
        $sql_check_conflict = "SELECT * FROM order_items 
                               WHERE ISBN = ? 
                               AND type = 'Borrow' 
                               AND orderID != ? 
                               AND (
                                    (startDate BETWEEN ? AND ?) OR 
                                    (endDate BETWEEN ? AND ?) OR 
                                    (? BETWEEN startDate AND endDate) OR 
                                    (? BETWEEN startDate AND endDate)
                               )";
        $stmt_check_conflict = $connection->prepare($sql_check_conflict);
        $stmt_check_conflict->bind_param("sissssss", $isbn, $orderID, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
        $stmt_check_conflict->execute();
        $conflict_result = $stmt_check_conflict->get_result();

        if ($conflict_result->num_rows > 0) {
            echo "<script>alert('Cannot update reservation. Another reservation conflicts with the selected dates.'); window.history.back();</script>";
            exit(); // إيقاف العملية
        }
    }

    // بداية الجزء الذي طلبت إضافته
    // تحديث الطلب في order_items مع الحفاظ على القيم الأصلية
    $sql_update_items = "UPDATE order_items 
                         SET startDate = COALESCE(NULLIF(?, ''), startDate), 
                             endDate = COALESCE(NULLIF(?, ''), endDate) 
                         WHERE orderID = ? AND type = 'Borrow'";

    $stmt_update_items = $connection->prepare($sql_update_items);
    $stmt_update_items->bind_param("ssi", $startDate, $endDate, $orderID);

    // تحديث العنوان فقط إذا كان هناك تغيير
    $sql_update_orders = "UPDATE orders 
                          SET address = COALESCE(NULLIF(?, ''), address) 
                          WHERE orderID = ?";

    $stmt_update_orders = $connection->prepare($sql_update_orders);
    $stmt_update_orders->bind_param("si", $address, $orderID);
    // نهاية الجزء الذي طلبت إضافته

    if ($stmt_update_items->execute() && $stmt_update_orders->execute()) {
        echo "<script>alert('Reservation updated successfully!'); window.location.href='orders.php';</script>";
    } else {
        echo "<script>alert('Error updating reservation.'); window.history.back();</script>";
    }
}
?>
