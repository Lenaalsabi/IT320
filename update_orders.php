<?php


ini_set('display_errors', 1);
error_reporting(E_ALL);

include('db_connect.php');

class UpdateOrder {
    private $connection;

    public function __construct($db_connection) {
        $this->connection = $db_connection;
    }

    public function updateOrderStatus() {
        $query = "SELECT orderID, created_at, status FROM orders WHERE status IN ('Pending', 'Shipped')";
        $result = $this->connection->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orderID = $row['orderID'];
                $orderDate = $row['created_at'];
                $status = $row['status'];

                // تجاهل الوقت في orderDate
                $orderDate = date('Y-m-d', strtotime($orderDate)); 
                $daysPassed = (strtotime(date('Y-m-d')) - strtotime($orderDate)) / (60 * 60 * 24);

                if ($status == 'Pending' && $daysPassed >= 3) {
                    $this->changeStatus($orderID, 'Shipped');
                } elseif ($status == 'Shipped' && $daysPassed >= 7) {
                    $this->changeStatus($orderID, 'Delivered');
                }

            }
        }
    }

    private function changeStatus($orderID, $newStatus) {
        $updateQuery = "UPDATE orders SET status = ? WHERE orderID = ?";
        $stmt = $this->connection->prepare($updateQuery);
        $stmt->bind_param("si", $newStatus, $orderID);
        $stmt->execute();
        $stmt->close();
    }
}

// تشغيل الكود لتحديث الطلبات
$updateOrder = new UpdateOrder($connection);
$updateOrder->updateOrderStatus();

?>


