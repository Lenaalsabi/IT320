<?php
session_start();
include 'db_connect.php'; // Include the database connection

if (!isset($_SESSION['customerID'])) {
    header("Location: homepage.html"); // Redirect to homepage if not logged in
    exit();
}

$customerID = $_SESSION['customerID']; 
$query = "SELECT orderID, totalPrice, address, status FROM orders WHERE customerID = ? ORDER BY created_at DESC";
$stmt = $connection->prepare($query);  // This will now work because $connection is defined
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
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
    <style>.suggestions-box {
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
.cancel-order-btn {
    background-color:rgb(232, 160, 152); /* Red background */
    color: white; /* White text */
    font-size: 17px; /* Font size */
    font-weight: bold; /* Make the text bold */
    padding: 10px 10px; /* Add padding for better spacing */
    border: none; /* Remove default border */
    border-radius: 9px; /* Rounded corners */
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s, transform 0.2s; /* Smooth hover effect */
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
    <input type="text" name="query" id="search-input" placeholder="Search for a book..." autocomplete="off" required>
    <div id="suggestions" class="suggestions-box"></div>
</form>

           

            <nav class="link-section">
                <div class="icons">
                    <a href="wishlist.php">
                        <img src="images/love.png" alt="Wishlist" id="wishlist-icon">
                        <p>Wishlist</p>
                    </a>
                    <a href="cart.html">
                        <img src="images/cart.png" alt="Cart" id="cart-icon">
                        <p>Cart</p>
                    </a>
                    <div class="profile-container2">
                        <a href="#" id="profile-icon">
                            <img src="images/user.png" alt="Profile">
                            <p>Profile</p>
                        </a>
                        <div class="profile-dropdown">
                            <a href="profile.php">Update Profile</a>
                            <a href="orders.html">My Orders</a>
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
<!-- rana-->
        <section class="order-section">
            <h2 class="section-title"><span class="highlight2"> Current</span>orders</h2>
            <?php while ($order = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-details">
                        <p class="order-id">Order ID: <?php echo $order['orderID']; ?></p>
                        <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> <?php echo $order['totalPrice']; ?></p>
                        <p class="delivery-address">Delivery Address: <?php echo $order['address']; ?></p>
                        <p class="order-status">Status: <span class="highlight3"><?php echo $order['status']; ?></span></p>

                        <?php if ($order['status'] == 'Pending'): ?>
                            <button class="cancel-order-btn" onclick="cancelOrder(<?php echo $order['orderID']; ?>)">cancel reservation </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
                    </div>
               
            <!--<div class="order-card">
                <div class="book-images">
                    <img class="book-image" src="images/book1.jpg" alt="Book Cover">
                    <img class="book-image" src="images/book2.jpg" alt="Book Cover">
                </div>
                <div class="order-details">
                    <p class="order-id">Order ID: #123456</p>
                    <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> 45.00</p>
                    <p class="delivery-address">Delivery Address: Riyadh, Saudi Arabia</p>
                    <p class="order-status">Status:<span class="highlight3">  Pending</span></p>
                </div>
            </div>
            <div class="order-card">
                <div class="book-images">
                    <img class="book-image" src="images/book3.jpg" alt="Book Cover">
                    <img class="book-image" src="images/book4.jpg" alt="Book Cover">
                </div>
                <div class="order-details">
                    <p class="order-id">Order ID: #123457</p>
                    <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> 10.50</p>
                    <p class="delivery-address">Delivery Address: Riyadh, Saudi Arabia</p>
                    <p class="reservation-period">Reservation period: 8/3/2025 - 25/3/2025</p>
                    <p class="order-status">Status: <span class="highlight3">  Shipped</span></p>
                </div>
                <span class="edit-label" id="edit-label1"><a href="edit.html">Edit</a></span>


            </div>-->
        </section>
        <section class="order-section">
            <h2 class="section-title"><span class="highlight2"> Past</span> orders</h2>
            <div class="order-card">
                <div class="book-images">
                    <img class="book-image" src="images/book5.jpg" alt="Book Cover">
                    <img class="book-image" src="images/book6.jpg" alt="Book Cover">
                </div>
                <div class="order-details">
                    <p class="order-id">Order ID: #123458</p>
                    <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> 35.00</p>
                    <p class="delivery-address">Delivery Address: Riyadh, Saudi Arabia</p>
                    <p class="order-status">Status:<span class="highlight3"> Delivered</span></p>
                </div>
            </div>
            <div class="order-card">
                <div class="book-images">
                    <img class="book-image" src="images/book6.jpg" alt="Book Cover">
                    <img class="book-image" src="images/book2.jpg" alt="Book Cover">
                </div>
                <div class="order-details">
                    <p class="order-id">Order ID: #123459</p>
                    <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> 80.30</p>
                    <p class="delivery-address">Delivery Address: Riyadh, Saudi Arabia</p>
                    <p class="order-status">Status:<span class="highlight3"> Delivered</span></p>
                </div>
            </div>
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
    <div class="bottom-bar">
        <p>  Terms and Conditions
privacy policy<br>&copy; 2024 mawj company . All rights reserved</p>
    </div>
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

</body>
</html>