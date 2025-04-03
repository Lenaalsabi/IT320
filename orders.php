<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include'auth.php';
include 'db_connect.php'; // Include the database connection
include'update_orders.php'; //status updating

if (!isset($_SESSION['customerID'])) {
    header("Location: homepage.html"); // Redirect to homepage if not logged in
    exit();
}

$customerID = $_SESSION['customerID']; 

// استعلام لعرض الطلبات الحالية فقط
$query_current = "
SELECT 
    orders.orderID, orders.customerID, orders.totalPrice AS orderTotalPrice, 
    orders.status AS orderStatus, orders.address, orders.created_at,
    order_items.ISBN, order_items.type AS orderType, order_items.quantity,
    order_items.startDate, order_items.endDate, order_items.totalPrice AS itemTotalPrice, 
    order_items.status AS itemStatus
FROM orders
JOIN order_items ON orders.orderID = order_items.orderID
WHERE orders.customerID = ? AND orders.status NOT IN ('Delivered' , 'Cancelled')
ORDER BY orders.created_at DESC
";

// استعلام لعرض الطلبات السابقة فقط
$query_past = "
SELECT 
    orders.orderID, orders.customerID, orders.totalPrice AS orderTotalPrice, 
    orders.status AS orderStatus, orders.address, orders.created_at,
    order_items.ISBN, order_items.type AS orderType, order_items.quantity,
    order_items.startDate, order_items.endDate, order_items.totalPrice AS itemTotalPrice, 
    order_items.status AS itemStatus
FROM orders
JOIN order_items ON orders.orderID = order_items.orderID
WHERE orders.customerID = ? AND orders.status IN ('Delivered', 'Cancelled')
ORDER BY orders.created_at DESC
";

// استعلام للطلبات الحالية
$stmt_current = $connection->prepare($query_current);
$stmt_current->bind_param("i", $customerID);
$stmt_current->execute();
$result_current = $stmt_current->get_result();

// استعلام للطلبات السابقة
$stmt_past = $connection->prepare($query_past);
$stmt_past->bind_param("i", $customerID);
$stmt_past->execute();
$result_past = $stmt_past->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .no-orders-message {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #888;
            text-align: center;
            margin-top: 50px;
        }
        
.return-button {
    background-color: rgb(232, 160, 152); /* Red background */
    color: white; /* White text */
    font-size: 17px; /* Font size */
    font-weight: bold; /* Make the text bold */
    width: 30%;
    max-width: 180px; /* تحديد عرض ثابت */
    height: 35px; /* تحديد ارتفاع ثابت */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
     position: absolute;
    right: 20px; /* تحريك الزر إلى أقصى اليمين */
    top: 160px;
}



        .return-button-container {
        display: flex;
        justify-content: flex-end;
        }
        
        .return-button:hover {
            background-color: #F8D49D;
            color: white;
        }
        
        .cancel-order-btn {
   background-color: rgb(232, 160, 152); /* Red background */
    color: white; /* White text */
    font-size: 17px; /* Font size */
    font-weight: bold; /* Make the text bold */
    width: 30%;
    max-width: 180px; /* تحديد عرض ثابت */
    height: 35px; /* تحديد ارتفاع ثابت */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
     position: absolute;
    right: 20px; /* تحريك الزر إلى أقصى اليمين */
    top: 160px;
        }
        
            .cancel-order-btn:hover {
            background-color: #F8D49D;
            color: white;
        }
        .cancel-order-container{
             display: flex;
    justify-content: flex-end;  
        }

        .order-details-btn {
  background-color: rgb(232, 160, 152); /* Red background */
    color: white; /* White text */
    font-size: 17px; /* Font size */
    font-weight: bold; /* Make the text bold */
    width: 30%;
    max-width: 180px; /* تحديد عرض ثابت */
    height: 35px; /* تحديد ارتفاع ثابت */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
     position: absolute;
    right: 20px; /* تحريك الزر إلى أقصى اليمين */
    top: 120px;
        }
        
        .order-details-btn:hover {
          background-color: #F8D49D;
            color: white;  
        }
    
        .order-details-btn-container{
        display: flex;
        justify-content: flex-end;  
        }
        
    
 .edit-order-btn {
    background-color: rgb(232, 160, 152); /* Red background */
    color: white; /* White text */
    font-size: 17px; /* Font size */
    font-weight: bold; /* Make the text bold */
    width: 30%; /* Set width as a percentage of the parent container */
    max-width: 180px; /* Limit the width to 180px */
    height: 35px; /* Fixed height */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    right: 20px; /* Move button to the far right */
    top: 80px;
}

.edit-order-btn:hover {
    background-color: #F8D49D;
    color: white;
}

.edit-order-container {
    display: flex;
    justify-content: flex-end;
}


            .suggestions-box {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #ccc;
    border-top: none;
    z-index: 9999;
    max-height: 200px;
    overflow-y: auto;
    display: none;
}
.suggestions-box div {
    padding: 10px;
    cursor: pointer;
}

.suggestions-box div:hover {
    background-color: #f2cc8f;
}
 #editFormContainer {
    display: none; 
    position: fixed; 
    top: 50%; 
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    border-radius: 10px; 
    box-shadow: 0px 0px 10px rgba(200, 200, 200, 0.6); /* ظل خفيف متناسق */
    z-index: 1000; 
    width: 300px;
    background-color:#FFFCF5;
}

#editReservationForm {
    display: flex;
    flex-direction: column;
     background-color: #FFFCF5;
}

#editReservationForm label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

#editReservationForm input {
    padding: 10px;
    border: 1px solid #988414; /* لون الإطار */
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#editReservationForm input:focus {
    border-color: #988414;
    outline: none;
    box-shadow: 0px 0px 5px rgba(152, 132, 20, 0.6); /* ظل الإدخال عند التركيز */
}

#editReservationForm button {
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

#editReservationForm button[type="submit"] {
    background: #988414; /* لون الزر */
    color: white;
    font-weight: bold;
}

#editReservationForm button[type="submit"]:hover {
    background: #7a6b10; /* لون أغمق عند التحويل */
}

#editReservationForm button[type="button"] {
    background: #ccc;
    color: black;
    margin-top: 5px;
}

#editReservationForm button[type="button"]:hover {
    background: #999;
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
    
                <div class="orders-container">
        <div class="title-section">
            <div class="horizontal-line"></div>
            <div class="title">
                <h1 class="page-title">My Orders</h1>
            </div>
            <div class="horizontal-line"></div>
        </div>
            
                </div>

 
<!-- Current Orders -->
<section class="order-section">
    <h2 class="section-title"><span class="highlight2">Current</span> orders</h2>
    <?php $hasOrders = false; ?>
    <?php while ($order = $result_current->fetch_assoc()): ?>
        <?php $hasOrders = true; ?>
        <div class="order-card">
            <div class="order-details">
                <p class="order-id">Order ID: <?php echo $order['orderID']; ?></p>
                <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> <?php echo $order['orderTotalPrice']; ?></p>
                <p class="order-type">Order Type: <span><?php echo $order['orderType']; ?></span></p>
                <p class="delivery-address">Delivery Address: <?php echo $order['address']; ?></p>
                <p class="order-status" data-order-id="<?php echo $order['orderID']; ?>">
                    Status: <span class="highlight3"><?php echo $order['orderStatus']; ?></span>
                </p>
                <p class="order-date">Order Date: <?php echo $order['created_at']; ?></p>

<?php  if ($order['orderType'] !== 'Purchase') {  ?>  
                <div class="edit-order-btn-container">                                            
<button class="edit-order-btn" onclick="openEditForm('<?php echo $order['orderID']; ?>',
                               '<?php echo $order['orderType']; ?>',
                               '<?php echo $order['orderStatus']; ?>',
                               '<?php echo $order['startDate']; ?>',
                               '<?php echo $order['endDate']; ?>',
                               '<?php echo $order['address']; ?>',
                               event)">
    Edit
</button>
 </div>
<div id="editFormContainer" >
    <form id="editReservationForm" method="post" action="update_reservation.php">
        <input type="hidden" name="orderID" id="orderID">
        
        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" id="startDate" required>

        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" id="endDate" required>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required>

        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeEditForm()">Cancel</button>
    </form>
</div>
  <?php } ?>                
                <!-- Button to show order details -->
                <div class="order-details-btn-container">
                <?php
                $orderType = $order['orderType']; 

                if ($orderType == 'Borrow') {
                // إذا كان الطلب استعارة، يظهر زر تفاصيل الاستعارة
                echo '<button onclick="window.location.href=\'borrow_order_details.php?orderID=' . $order['orderID'] . '\';" class="order-details-btn">Show Details</button>';
                } else if ($orderType == 'Purchase') {
                // إذا كان الطلب شراء، يظهر زر تفاصيل الشراء
                echo '<button onclick="window.location.href=\'purchase_order_details.php?orderID=' . $order['orderID'] . '\';" class="order-details-btn">Show Details</button>';
                }
                ?>
                </div>
                                <!-- Button to cancel the order if status is 'Pending' -->
                <?php if ($order['orderStatus'] == 'Pending'): ?>
                    <div class="cancel-order-container">
                        <button class="cancel-order-btn" onclick="cancelOrder(<?php echo $order['orderID']; ?>)">Cancel</button>
                    </div>
                <?php endif; ?>
             

                                
            </div>
        </div>
    <?php endwhile; ?>
    <?php if (!$hasOrders): ?>
        <p class="no-orders-message">No current orders found.</p>
    <?php endif; ?>
</section>

<!-- Past Orders -->
<section class="order-section">
    <h2 class="section-title"><span class="highlight2">Past</span> orders</h2>
    <?php $hasPastOrders = false; ?>
    <?php while ($order = $result_past->fetch_assoc()): ?>
        <?php $hasPastOrders = true; ?>
        <div class="order-card">
            <div class="order-details">
                <p class="order-id">Order ID: <?php echo $order['orderID']; ?></p>
                <p class="price">Price: <span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> <?php echo $order['orderTotalPrice']; ?></p>
                <p class="order-type">Order Type: <span><?php echo $order['orderType']; ?></span></p>
                <p class="delivery-address">Delivery Address: <?php echo $order['address']; ?></p>
                <p class="order-status" data-order-id="<?php echo $order['orderID']; ?>">
                    Status: <span class="highlight3"><?php echo $order['orderStatus']; ?></span>
                </p>
                <p class="order-date">Order Date: <?php echo $order['created_at']; ?></p>

<?php  
if ($order['orderType'] !== 'Purchase' && $order['orderStatus'] !== 'Cancelled' && !($order['orderStatus'] === 'Delivered' && $order['orderType'] === 'Borrow' && $order['itemStatus'] === 'Returned')) { 
?>  
            <div class="edit-order-btn-container">             
    <button class="edit-order-btn" onclick="openEditForm('<?php echo $order['orderID']; ?>',
                   '<?php echo $order['orderType']; ?>',
                   '<?php echo $order['orderStatus']; ?>',
                   '<?php echo $order['startDate']; ?>',
                   '<?php echo $order['endDate']; ?>',
                   '<?php echo $order['address']; ?>',
                   event)">
        Edit
    </button>
                </div>
<?php } ?>

<div id="editFormContainer" >
    <form id="editReservationForm" method="post" action="update_reservation.php">
        <input type="hidden" name="orderID" id="orderID">
        
        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" id="startDate" required>

        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" id="endDate" required>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required>

        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeEditForm()">Cancel</button>
    </form>
</div>
                
                <!-- Show return button if the order type is 'Borrow' and item is not returned -->
                <?php if ($order['orderType'] == 'Borrow' && $order['itemStatus'] != 'Returned' && $order['orderStatus'] == 'Delivered'): ?>
                    <div class="return-button-container">
                        <form method="post" action="return_item.php?orderID=<?php echo $order['orderID']; ?>">
                            <button type="submit" class="return-button">Return</button>
                        </form>
                    </div>
                <?php endif; ?>

                
                <!-- Button to show order details -->
                <div class="order-details-btn-container">
                <?php
                $orderType = $order['orderType']; 

                if ($orderType == 'Borrow') {
                 // إذا كان الطلب استعارة، يظهر زر تفاصيل الاستعارة
                echo '<button onclick="window.location.href=\'borrow_order_details.php?orderID=' . $order['orderID'] . '\';" class="order-details-btn">Show Details</button>';
                } else if ($orderType == 'Purchase') {
                // إذا كان الطلب شراء، يظهر زر تفاصيل الشراء
                echo '<button onclick="window.location.href=\'purchase_order_details.php?orderID=' . $order['orderID'] . '\';" class="order-details-btn">Show Details</button>';
                }
                ?>
                </div>
               
            </div>
        </div>
    <?php endwhile; ?>
    
    <?php if (!$hasPastOrders): ?>
        <p class="no-orders-message">No past orders found.</p>
    <?php endif; ?>
</section>





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
        <script>
   function cancelOrder(orderID) {
    if (confirm("Are you sure you want to cancel this reservation?")) {
        fetch('cancel_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `orderID=${orderID}`
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); 
            if (data === "success") {
                alert("Reservation cancelled successfully");
                location.reload();
            } else {
                alert(data); 
            }
        })
        .catch(error => {
            alert("Connection error: " + error);
        });
    }
}

</script>
<script>
const searchInput = document.getElementById("search-input");
const suggestionsBox = document.getElementById("suggestions");

searchInput.addEventListener("input", function () {
    const query = this.value.trim();
    if (query.length < 2) {
        suggestionsBox.innerHTML = "";
        suggestionsBox.style.display = "none";
        return;
    }

    fetch(`search_suggestions.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            suggestionsBox.innerHTML = "";
            if (data.length > 0) {
                data.forEach(book => {
                    const div = document.createElement("div");
                    div.textContent = book.title;
                    div.onclick = () => {
                        window.location.href = `book_details.php?isbn=${book.ISBN}`;
                    };
                    suggestionsBox.appendChild(div);
                });
                suggestionsBox.style.display = "block";
            } else {
                suggestionsBox.style.display = "none";
            }
        });
});

</script>
<script>
function fetchOrders() {
    fetch('fetch_orders.php')
        .then(response => response.json())
        .then(data => {
            if (!data || Object.keys(data).length === 0) {
                console.error("No past orders found.");
                return;
            }

            let pastOrdersContainer = document.querySelector(".order-section:nth-of-type(2)");
            if (!pastOrdersContainer) {
                console.error("Past orders container not found.");
                return;
            }

            pastOrdersContainer.innerHTML = `<h2 class="section-title"><span class="highlight2"> Past</span> orders</h2>`;

            Object.values(data).forEach(order => {
                let orderHTML = `
                    <div class="order-card">
                        <div class="order-details">
                            <p class="order-id">Order ID: ${order.orderID}</p>
                            <p class="price"><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"> ${order.orderTotalPrice}</p>
                            <p class="delivery-address">Delivery Address: ${order.address}</p>
                            <p class="order-status">Status: <span class="highlight3">${order.orderStatus}</span></p>
                            <p class="order-date">Order Date: ${order.created_at}</p>
                            <div class="items">
                `;

if (!isPastOrder) {
    order.items.forEach(item => {
        orderHTML += `
            <p>ISBN: ${item.ISBN}, Type: ${item.orderType}, Quantity: ${item.quantity}</p>
        `;
    });
}

                    `;

                    if (item.orderType === 'Borrow' && item.itemStatus !== 'Returned' && order.orderStatus === 'Delivered') {
                        orderHTML += `
                            <div class="return-button-container">
                                <form method="post" action="return_item.php?orderID=${order.orderID}">
                                    <button type="submit" class="return-button">Return Item</button>
                                </form>
                            </div>
                        `;
                    }
                });

                orderHTML += `</div></div></div>`;
                pastOrdersContainer.innerHTML += orderHTML;
            });
        })
        .catch(error => console.error("Error fetching orders:", error));
}

// تحميل الطلبات عند فتح الصفحة
fetchOrders();

</script>
<script>
function openEditForm(orderID, orderType, orderStatus, startDate, endDate, address) {
    var modal = document.getElementById('editFormContainer');

    var orderIDField = document.getElementById('orderID');
    var startDateField = document.getElementById('startDate');
    var endDateField = document.getElementById('endDate');
    var addressField = document.getElementById('address');

    if (orderIDField) orderIDField.value = orderID;
    if (startDateField) startDateField.value = startDate;
    if (endDateField) endDateField.value = endDate;
    if (addressField) addressField.value = address;

    // الافتراضي: تمكين جميع الحقول
    startDateField.disabled = false;
    endDateField.disabled = false;
    addressField.disabled = false;

    // إذا كان الطلب استعارة (Borrow) وتم تسليمه، امنع تعديل العنوان وتاريخ البداية
    if (orderType === 'Borrow' && orderStatus === 'Delivered') {
        addressField.disabled = true;
        startDateField.disabled = true;
    } 
    // إذا كان الطلب استعارة (Borrow) وفي حالة Pending أو Shipped، اسمح بتعديل تاريخ البداية
    else if (orderType === 'Borrow' && (orderStatus === 'Pending' || orderStatus === 'Shipped')) {
        startDateField.disabled = false;
    }

    if (modal) modal.style.display = 'block';
}

// دالة لإغلاق الفورم
function closeEditForm() {
    var modal = document.getElementById('editFormContainer');
    if (modal) {
        modal.style.display = 'none';  // إخفاء النموذج
    }
}

</script>


</body>
</html>
 
