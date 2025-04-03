<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db_connect.php';

$orderID = isset($_GET['orderID']) ? intval($_GET['orderID']) : 0;
$order = null;

if ($orderID > 0) {
    // 🔹 جلب بيانات الطلب
    $sql_order = "SELECT orderID, created_at, address, status, totalPrice FROM orders WHERE orderID = ?";
    $stmt_order = $connection->prepare($sql_order);
    $stmt_order->bind_param('i', $orderID);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();

    if ($result_order->num_rows > 0) {
        $order = $result_order->fetch_assoc();

        // 🔹 تحديث الحالة إلى 'borrowed' عند تسليم الطلب
        if ($order['status'] === 'Delivered') {
            $sql_update = "UPDATE order_items SET status = 'Borrowed' WHERE orderID = ? AND (status IS NULL OR status = '')";
            $stmt_update = $connection->prepare($sql_update);
            $stmt_update->bind_param('i', $orderID);
            $stmt_update->execute();
        }

        // 🔹 تحديث الحالة إلى 'overdue' إذا تجاوز تاريخ الانتهاء
        $sql_overdue = "UPDATE order_items SET status = 'Overdue' WHERE orderID = ? AND status = 'Borrowed' AND endDate < CURDATE()";
        $stmt_overdue = $connection->prepare($sql_overdue);
        $stmt_overdue->bind_param('i', $orderID);
        $stmt_overdue->execute();

        // 🔹 جلب بيانات الكتب في الطلب
        $sql_items = "SELECT b.cover, b.title, o.ISBN, o.type, o.quantity, o.startDate, o.endDate, o.totalPrice, o.status 
                      FROM order_items o
                      JOIN book b ON o.ISBN = b.ISBN
                      WHERE o.orderID = ?";
        $stmt_items = $connection->prepare($sql_items);
        $stmt_items->bind_param('i', $orderID);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();

        // 🔹 التحقق من كل عنصر وتحديث حالته
        $currentDate = date('Y-m-d');

        while ($item = $result_items->fetch_assoc()) {
            $itemStatus = $item['status'];
            $endDate = $item['endDate'];

            if ($order['status'] == 'Delivered') {
                if ($itemStatus != 'Returned') { // لا تعدل المرتجعة
                    if ($currentDate > $endDate) {
                        $newStatus = 'Overdue';
                    } elseif ($currentDate <= $endDate && $itemStatus == 'Overdue') {
                        $newStatus = 'Borrowed'; // إرجاع الحالة إلى Borrowed إذا تم تعديل endDate
                    } else {
                        $newStatus = $itemStatus; // لا تغيير
                    }

                    // تحديث الحالة في قاعدة البيانات إذا تغيرت
                    if ($newStatus != $itemStatus) {
                        $updateSql = "UPDATE order_items SET status = ? WHERE orderID = ? AND ISBN = ?";
                        $updateStmt = $connection->prepare($updateSql);
                        if ($updateStmt) {
                            $updateStmt->bind_param('sis', $newStatus, $orderID, $item['ISBN']);
                            $updateStmt->execute();
                        }
                    }
                }
            }
        }

        // 🔄 إعادة تنفيذ الاستعلام بعد التحديث
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
    } else {
        echo "No order found with this ID.";
    }
} else {
    echo "Invalid order ID.";
}
?>



<!DOCTYPE html>
<head>
<html lang="en">
 <title>Orders Details - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<style>
      
.orders-details-container {
    max-width: 80%;
    margin: 2em auto;
    background: #FFFCF5;
    padding: 1.5em;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.orders-container p {
    font-size: 1.1em;
    color: #34272158;
    margin-bottom: 0.5em;
}

.orders-container strong {
    color: #988414;
}

/* تنسيق الجدول */
.order-items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

.order-items-table th, .order-items-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    font-size: 1em;
}

.order-items-table th {
    background-color: #988414;
    color: white;
    font-weight: bold;
}

.order-items-table tr:nth-child(even) {
    background-color: #FFFCF5;
}

.order-items-table tr:hover {
    background-color: #f5f1e9;
}

/* تنسيق الصور في الجدول */
.order-items-table img {
    width: 50px;
    height: auto;
    border-radius: 5px;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
}

/* تحسين التصميم للأجهزة الصغيرة */
@media (max-width: 768px) {
    .orders-container {
        max-width: 95%;
    }
    .order-items-table th, .order-items-table td {
        padding: 8px;
        font-size: 0.9em;
    }
}

</style>
</head>
<body>
 <header>
        <div class="header">
            <div class="logo-section">
                <div class="horizontal-line"></div>
                <div class="logo">
                    <a href="homebage2.php">
                        <img src="images/logo.png" alt="موج Logo" id="logo">
                    </a>
                </div>
                <div class="horizontal-line"></div>
            </div>
        
            <form class="search-section" id="searchForm" onsubmit="return false;">
                <img src="images/search.png" alt="search" class="search-icon">
                <input type="text" name="query" id="search-input" placeholder="Search for a book..." autocomplete="on" required>
                <div id="suggestions" class="suggestions-box"></div>
            </form>
            
            <nav class="link-section">
                <div class="icons">
                    <a href="wishlist.php">
                        <img src="images/love.png" alt="Wishlist" id="wishlist-icon">
                        <p>Wishlist</p>
                    </a>
                    <a href="cart.php">
                        <img src="images/cart.png" alt="Cart" id="cart-icon">
                        <p>Cart</p>
                    </a>
                    <div class="profile-container2">
                        <a href="#" id="profile-icon">
                            <img src="images/user.png" alt="Profile">
                            <p>Profile</p>
                        </a>
                        <div class="profile-dropdown">
                            <a href="profile.php">Profile</a>
                            <a href="orders.php">My Orders</a>
                        </div>
                    </div>
                    <a href="books.php">
                        <img src="images/books.png" alt="Books" id="books-icon">
                        <p>Books</p>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="orders-details-container">
            <h1 class="page-title">Order Details</h1>
            <?php if ($order): ?>
                <div class="order-info">
                    <p><strong>Order ID:</strong> <?php echo $order['orderID']; ?></p>
                    <p><strong>Order Date:</strong> <?php echo $order['created_at']; ?></p>
                    <p><strong>Delivery Address:</strong> <?php echo $order['address']; ?></p>
                    <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
                    <p><strong>Total Price:</strong> <?php echo number_format($order['totalPrice'], 2); ?></p>
                </div>

                <h3 class="order-items-title">Order Items</h3>
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>ISBN</th>
                            <th>Quantity</th>
                            <th>Order Type</th>
                            <th>Item Price</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $result_items->fetch_assoc()): ?>
                            <tr>
                                <td><img src="images/<?php echo $item['cover']; ?>" alt="Book Cover"></td>
                                <td><?php echo $item['title']; ?></td>
                                <td><?php echo $item['ISBN']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo $item['type']; ?></td>
                                <td><?php echo number_format($item['totalPrice'], 2); ?></td>
                                <td><?php echo $item['startDate']; ?></td>
                                <td><?php echo $item['endDate']; ?></td>
                                <td>
                                    <?php if ($order['status'] === 'Delivered'): ?>
                                        <?php echo $item['status']; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No order found with this ID.</p>
            <?php endif; ?>
        </div>
    </main>

       <footer>
        <div class="footer-section footer-logo">
            <img src="images/logo.png" alt="footer-logo" width="320">
        </div>
        <div class="footer-section social-media">
            <h3>SOCIAL MEDIA</h3>
            <ul class="social-icons">
                <li><a href="#"><img src="images/twitter.png" alt="Twitter"></a></li>
                <li><a href="#"><img src="images/facebook.png" alt="Facebook"></a></li>
                <li><a href="#"><img src="images/insta.png" alt="Instagram"></a></li>
                <li>@official_mawj</li>
            </ul>
        </div>
        <div class="footer-section contact-us">
            <h3>CONTACT US</h3>
            <ul>
                <li><a href="#"><img src="images/phone1.png" alt="Phone"> +123 165 788</a></li>
                <li><a href="mailto:mawj@gmail.com"><img src="images/email1.png" alt="Email"> mawj@gmail.com</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>
