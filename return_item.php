<?php
session_start();
include 'db_connect.php'; // Include the database connection

if (!isset($_SESSION['customerID'])) {
    header("Location: homepage.html"); // Redirect to homepage if not logged in
    exit();
}

$customerID = $_SESSION['customerID']; // الحصول على customerID من الجلسة
$orderID = $_GET['orderID']; // الحصول على orderID من الرابط أو الفورم

// استعلام لتحديث حالة الـ item إلى "Returned"
$query = "
UPDATE order_items
JOIN orders ON order_items.orderID = orders.orderID
SET order_items.status = 'Returned'
WHERE orders.customerID = ? AND order_items.orderID = ?
";

// تحضير الاستعلام
$stmt = $connection->prepare($query);
$stmt->bind_param("ii", $customerID, $orderID);

// تنفيذ الاستعلام
if ($stmt->execute()) {
    echo "<script>alert('The item has been successfully returned.'); window.location.href='orders.php';</script>";
} else {
    echo "<script>alert('An error occurred while returning the item. Please try again.'); window.location.href='orders.php';</script>";
}

$stmt->close();
?>
