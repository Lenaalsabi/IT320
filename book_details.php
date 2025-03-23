<?php
include 'db_connect.php';

if (!isset($_GET['isbn'])) {
    echo "<h2>Book not found.</h2>";
    exit();
}

$isbn = $_GET['isbn'];
$query = "SELECT * FROM book WHERE ISBN = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $isbn);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>Book not found.</h2>";
    exit();
}

$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Details - موج</title>
    <link rel="stylesheet" href="stylesBD.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<header>
    <div class="header">
        <div class="logo-section">
            <div class="horizontal-line"></div>
            <div class="logo">
                <a href="homebage2.html">
                    <img src="images/logo.png" alt=" موج Logo" id="logo">
                </a>
            </div>
            <div class="horizontal-line"></div>
        </div>

        <nav class="link-section">
            <div class="icons">
                <a href="wishlist.html">
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

<br>
<div class="book_container">
    <button class="wishlist-btn" style="background-color: transparent; font-size: 37px; right: -30px; top:203px;" onclick="toggleWishlist(this)">♥</button>

    <div class="book_img">
        <img src="uploads/<?php echo $book['cover']; ?>" alt="book image">
    </div>

    <div class="book_info1">
        <h1 class="title1"><strong><?php echo $book['title']; ?></strong></h1>
        <h4><?php echo $book['Author']; ?></h4>
        <br>
        <p><span><img src="images/riyal-removebg-preview.png" style="height: 17px"></span><strong> <?php echo $book['price']; ?></strong></p>
        <br>
        <p style="line-height: 25px;">
            <?php echo substr($book['description'], 0, 100); ?>...
            <a href="#" id="read-more" onclick="toggleText(event)">Read More</a>
            <span id="more-text" style="display: none;"><?php echo substr($book['description'], 100); ?></span>
        </p>
        <br>
        <button class="add" onclick="window.location.href='cart.html'">ADD TO CART ＋</button>
        <button class="borrow" onclick="window.location.href='borrowing.html'">BORROW IT </button>
    </div>

    <div class="book_info2">
        <h2 style="font-size: 28px; color: #988414;"> More Details </h2><br>
        <hr style="width: 90%;">
        <h3>ISBN: <?php echo $book['ISBN']; ?></h3><br>
        <h3>Author: <?php echo $book['Author']; ?></h3><br>
        <h3>Genre: <?php echo $book['Genre']; ?></h3><br>
       
    </div>
</div>

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
    <p>Terms and Conditions privacy policy<br>&copy; 2024 mawj company. All rights reserved</p>
</div>


   

</body>
</html>
