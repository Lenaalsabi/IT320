<?php
include('auth.php');
include 'db_connect.php';

if (!isset($_SESSION['customerID'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

$customerID = $_SESSION['customerID'];

$query = "
SELECT 
    o.orderID, o.totalPrice AS orderTotalPrice, o.status AS orderStatus, 
    o.address, o.created_at, oi.ISBN, oi.type AS orderType, 
    oi.quantity, oi.startDate, oi.endDate, oi.totalPrice AS itemTotalPrice, 
    oi.status AS itemStatus
FROM orders o
JOIN order_items oi ON o.orderID = oi.orderID
WHERE o.customerID = ? AND o.status IN ('Delivered', 'Cancelled')
ORDER BY o.created_at DESC
";

$stmt = $connection->prepare($query);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orderID = $row['orderID'];
    
    if (!isset($orders[$orderID])) {
        $orders[$orderID] = [
            "orderID" => $orderID,
            "orderTotalPrice" => $row['orderTotalPrice'],
            "orderStatus" => $row['orderStatus'],
            "address" => $row['address'],
            "created_at" => $row['created_at'],
            "items" => []
        ];
    }

    $orders[$orderID]["items"][] = [
        "ISBN" => $row['ISBN'],
        "orderType" => $row['orderType'],
        "quantity" => $row['quantity'],
        "startDate" => $row['startDate'],
        "endDate" => $row['endDate'],
        "itemTotalPrice" => $row['itemTotalPrice'],
        "itemStatus" => $row['itemStatus']
    ];
}

echo json_encode($orders);
?>
