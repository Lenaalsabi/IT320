
<?php
include 'db_connect.php';
$query = "SELECT * FROM book";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books - موج</title>
     <style>
        .book-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            padding: 30px;
        }

        .book-card {
            width: 280px;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: 0.3s ease;
            font-size: 16px; 
            position: relative;
        }

        .book-card img {
            width: 100%;
            height: 300px;
            border-radius: 5px;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }

        .price {
            font-weight: bold;
            margin: 10px 0;
        }

        .wishlist-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #999;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .wishlist-btn.active {
            color: red;
        }
    </style>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        
             
        <div class="header">
            <div class="logo-section">
                <div class="horizontal-line"></div>
                <div class="logo">
                    <a href="homebage2.html">
                        <img src="images/logo.png" alt="موج Logo" id="logo">
                    </a>
                </div>
                <div class="horizontal-line"></div>
            </div>
        
            <div class="search-section">
                <img src="images/search.png" alt="search" class="search-icon">
                <input type="text" placeholder="Search">
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
                    <a href="books.html">
                        <img src="images/books.png" alt="Books" id="books-icon">
                        <p>Books</p>
                    </a>
                </div>
            </nav>
        </div>
        
    </header>

<main>
    <div class="title-section">
    <div class="horizontal-line"></div>
    <div class="title">
    <h1 class="page-title">Popular Books</h1>
    </div>
    <div class="horizontal-line"></div>
</div>
<div class="book-container">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="book-card">
            <button class="wishlist-btn" onclick="toggleWishlist(this)">♥</button>
            <img src="uploads/<?php echo $row['cover']; ?>" alt="Book Cover">
            <h3><?php echo $row['title']; ?></h3>
            <p><?php echo $row['Author']; ?></p>
            <p class="price">
                <span><img src="images/riyalyellow.png" style="width:14px;height:14px;"></span>
                <?php echo $row['price']; ?>
            </p>
          <button class="button primary" onclick="window.location.href='book_details.php?isbn=<?php echo $row['ISBN']; ?>'">Buy Now</button>

        </div>
    <?php } ?>
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
    <p>Terms and Conditions | Privacy Policy<br>&copy; 2024 Mawj Company. All rights reserved</p>
</div>
<script>
    function toggleWishlist(button) {
        button.classList.toggle("active");
    }
</script>
</body>
</html>
