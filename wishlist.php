<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['customerID'])) {
    header("Location: login.php");
    exit;
}

$customerID = $_SESSION['customerID'];

$query = "SELECT book.* FROM wishlist 
          JOIN book ON wishlist.ISBN = book.ISBN 
          WHERE wishlist.customerID = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<style>
body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
}

main {
    flex: 1;
}

footer {
    background-color: #f8f8f8;
    text-align: center;
    padding: 20px;
    width: 100%;
    margin-top: auto; 
}
.wishlist-container {
    max-width: 100%;
    margin: 40px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0);
}

.wishlist {
    list-style: none;
    padding: 0;
}

.wishlist-item {
    display: flex;
    align-items: center; 
    justify-content: space-between; 
    width: 100%;
    padding-top: 15px;
    padding-bottom: 15px;
    padding-right: 135px;
padding-left:135px ;
    border-bottom: 1px solid #ddd;
}


.wishlist-item img {
    width: 120px; 
    height: auto;
    border-radius: 5px;
}


.book-info {
    flex: 1; 
    display: flex;
    flex-direction: column;
    margin-left: 15px;
}


.book-info h3 {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 5px;
}


.book-info p {
    font-size: 16px;
    color: #666;
    margin-bottom: 5px;
}


.book-info .price {
    font-size: 14px;
    color: #95862F;
    font-weight: bold;
}


.buttons {
    display: flex;
    flex-direction: column; 
    align-items: flex-end; 
    gap: 10px; 
}


.cart-btn {
    width: 180px; 
    padding: 10px;
    text-align: center;
    background-color: #FDE9C8;
    color: #2f231e;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
}
.remove-btn {
    width: 180px; 
    padding: 10px;
    text-align: center;
    background-color: #f7ead7;
    color: #2f231e;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
}
.cart-btn:hover, .remove-btn:hover {
    background-color: #F8D49D;
    color: #fff;
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
                    <a href="wishlist.html">
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
    <div class="title-section">
        <div class="horizontal-line"></div>
        <div class="title">
            <h1 class="page-title">My Wishlist</h1>
        </div>
        <div class="horizontal-line"></div>
    </div>

    <div class="wishlist-container">
        <ul class="wishlist">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <li class="wishlist-item">
                    <img src="uploads/<?php echo $row['cover']; ?>" alt="<?php echo $row['title']; ?>">
                    <div class="book-info">
                        <h3><?php echo $row['title']; ?></h3>
                        <p class="author"><?php echo $row['Author']; ?></p>
                        <p class="price">
                            <span><img src="images/riyalyellow.png" style="width:13px;height:13px;"></span>
                            <?php echo $row['price']; ?>
                        </p>
                        <div class="buttons">
                        <button class="cart-btn" onclick="addToCart('<?php echo $row['ISBN']; ?>')">Add to Cart</button>

                            <button class="remove-btn" onclick="removeFromWishlist('<?php echo $row['ISBN']; ?>', this)">Remove</button>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
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
        <p>  Terms and Conditions<br>privacy policy<br>&copy; 2024 mawj company . All rights reserved</p>
    </div>
<script>
function removeFromWishlist(ISBN, button) {
    fetch('remove_from_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `ISBN=${ISBN}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            button.closest(".wishlist-item").remove();
            alert("The book has been removed from the wishlist  ");
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error("Error:", error));
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
function addToCart(isbn) {
    fetch('add_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `isbn=${isbn}&quantity=1`
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        alert("Item added to cart!");
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>
</body>
</html>

</body>
</html>

<?php
$stmt->close();
$connection->close();
?>
