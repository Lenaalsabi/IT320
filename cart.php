<?php
include 'auth.php';
include 'db_connect.php';

$userId = $_SESSION['customerID'];


$sqlCheckCart = "SELECT cartID FROM Cart WHERE customerID = ?";
$stmtCheckCart = $connection->prepare($sqlCheckCart);
$stmtCheckCart->bind_param("i", $userId);
$stmtCheckCart->execute();
$resultCheckCart = $stmtCheckCart->get_result();

if ($resultCheckCart->num_rows === 0) {
    $sqlCreateCart = "INSERT INTO Cart (customerID) VALUES (?)";
    $stmtCreateCart = $connection->prepare($sqlCreateCart);
    $stmtCreateCart->bind_param("i", $userId);
    $stmtCreateCart->execute();

    if ($stmtCreateCart->affected_rows > 0) {
        // Cart created
        $cartId = $connection->insert_id; // Get the newly created cartID
        header("Location: cart.php");
        // echo "Cart created for user.";
    } else {
        // Cart  failed
        echo "Error creating cart.";
        exit; // Stop execution
    }
    $stmtCreateCart->close();


    //
    $stmtCheckCart->execute();
    $resultCheckCart = $stmtCheckCart->get_result();
}

if ($resultCheckCart->num_rows > 0) {
    $rowCart = $resultCheckCart->fetch_assoc();
    $cartId = $rowCart['cartID'];

    // Now proceed with fetching cart items
    $cartItems = [];
    $totalPrice = 0;

    $_SESSION['total_price'] = $totalPrice; // Store in session

    $sqlCartItems = "SELECT cart_items.ISBN, Book.Title, Book.Author, Book.Price, cart_items.quantity, Book.cover 
                     FROM cart_items 
                     JOIN Book ON cart_items.ISBN = Book.ISBN
                     WHERE cart_items.cartID = ?";
    $stmtCartItems = $connection->prepare($sqlCartItems);
    $stmtCartItems->bind_param("i", $cartId);
    $stmtCartItems->execute();
    $resultCartItems = $stmtCartItems->get_result();


    if ($resultCartItems->num_rows > 0) {
        while ($row = $resultCartItems->fetch_assoc()) {
            $cartItems[] = $row;
            $totalPrice += $row['Price'] * $row['quantity'];
        }
    }

    /*
   $totalPrice = 0;
   foreach ($cartItems as $item) {
       $totalPrice += $item['Price'] * $item['quantity'];
   }*/
    $_SESSION['total_price'] = $totalPrice; // Store in session

    $stmtCartItems->close();
} else {
    $cartItems = []; // Empty cart if no cart found
    $totalPrice = 0;
}

$stmtCheckCart->close();






//
//
//
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];

    // Validate quantity
    if (!is_numeric($quantity) || $quantity < 1) {
        $quantity = 1;
    }

    // Increment/decrement
    if (isset($_POST['increment'])) {
        $quantity = $quantity + 1; // correct way to increment
    } elseif (isset($_POST['decrement'])) {
        $quantity = $quantity - 1; // correct way to decrement
        if ($quantity < 1) {
            $quantity = 1;
        }
    }

    // Update database
    $sqlUpdateQuantity = "UPDATE cart_items SET quantity = ? WHERE cartID = ? AND ISBN = ?";
    $stmtUpdateQuantity = $connection->prepare($sqlUpdateQuantity);
    $stmtUpdateQuantity->bind_param("iis", $quantity, $cartId, $isbn);
    $stmtUpdateQuantity->execute();
    $stmtUpdateQuantity->close();

    // Redirect
    header("Location: cart.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - موج</title>
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
                        <a href="profile.php">Update Profile</a>
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
            <h1 class="page-title">My Cart</h1>
        </div>
        <div class="horizontal-line"></div>
    </div>
    <div class="cart-content cart-container">

        <div class="cart-items">

            <?php if (empty($cartItems)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <!--src="uploads/-->
                        <img src="uploads/<?php echo htmlspecialchars($item['cover']); ?>" alt="<?php echo htmlspecialchars($item['Title']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['Title']); ?></h3>
                            <p><?php echo htmlspecialchars($item['Author']); ?></p>
                            <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> <?php echo number_format($item['Price'], 2); ?></p>
                            <form action="cart.php" method="POST">

                                <input type="hidden" name="action" value="update_quantity">
                                <input type="hidden" name="isbn" value="<?php echo htmlspecialchars($item['ISBN']); ?>">

                                <div class="quantity-controls">
                                    <button type="submit" class="quantity-btn" name="decrement" value="1"  >-</button>
                                    <input type="number"   name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" onchange="this.form.submit()" onchange="updateCartTotal()">
                                    <button type="submit" class="quantity-btn" name="increment" value="1" >+</button>
                                </div></form>
                        </div>
                        <button class="remove-btn"  onclick="removeItem(this, '<?php echo $item['ISBN']; ?>')">Remove</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- Cart Item 1
            <div class="cart-item">
                <img src="images/product-item1.jpg" alt="Book 1">
                <div class="item-details">
                    <h3>Simple Way Of Piece Life</h3>
                    <p>Armor Ramsy</p>
                    <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> 50.00</p>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decrementQuantity(this)">-</button>
                        <input type="number" value="1" min="1" class="quantity-input" onchange="updateCartTotal()">
                        <button class="quantity-btn" onclick="incrementQuantity(this)">+</button>
                    </div>
                </div>
                <button class="remove-btn" onclick="removeItem(this)">Remove</button>
            </div>

            <!-- Cart Item 2
            <div class="cart-item">
                <img src="images/product-item2.jpg" alt="Book 2">
                <div class="item-details">
                    <h3>Great Travel At Desert</h3>
                    <p>Sanclist Howdy</p>
                    <p class="price"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;margin-right:0;"></span> 40.00</p>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decrementQuantity(this)">-</button>
                        <input type="number" value="1" min="1" class="quantity-input" onchange="updateCartTotal()">
                        <button class="quantity-btn" onclick="incrementQuantity(this)">+</button>
                    </div>
                </div>
                <button class="remove-btn" onclick="removeItem(this)">Remove</button>
            </div>-->

        </div>

        <div class="cart-summary">
            <form action="order_items.php" method="POST">

                <h2>Order Summary</h2>
                <div class="summary-item">
                    <span>Items</span>
                    <span id="items-count"><?php echo count($cartItems); ?></span>
                    <!--  <span id="items-count">2</span>-->
                </div>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span id="subtotal"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> <?php echo number_format($totalPrice, 2); ?></span>
                    <!--   <span id="subtotal"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> 90.00</span>-->
                </div>
                <div class="summary-item total">
                    <span>Total</span>
                    <span id="total"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> <?php echo number_format($totalPrice, 2); ?></span>
                    <!-- <span id="total"><span><img src="images/riyal-removebg-preview.png" style="width:14px;height:14px;"></span> 90.00</span>-->
                </div>

                <button class="button primary checkout-btn" onclick="windows.location.href='checkout.php'" name="checkout">Checkout</button>
                <!--     <button class="button primary checkout-btn" onclick="window.location.href='checkout.php'">Checkout</button>-->
            </form>
        </div>
    </div>
    <footer>
        <div class="footer-section footer-logo">
            <img src="images/logo.png" alt="footer-logo" width="320" >
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
                <li><a href="mailto:@mawj@gmail.com"><img src="images/email1.png" alt="Email"> mawj@gmail.com</a></li>
            </ul>

        </div>

    </footer>
</main>
<script>

    function removeItem(button, isbn) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "removeCart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {

            button.closest('.cart-item').remove();
            updateCartTotal();//
            console.log("Response Success");
            console.log("Removing item:", button.closest('.cart-item'));
            button.closest('.cart-item').remove();
            console.log("Item removed.");
            updateCartTotal();
        };
        xhr.send("cartId=" + <?php echo $_SESSION['customerID']; ?> + "&isbn=" + isbn);
        location.reload();

    }




    function updateCartTotal() {
        let itemCount = 0;
        let subtotal = 0;

        document.querySelectorAll('.cart-item').forEach(item => {
            const price = parseFloat(item.querySelector('.price').textContent);
            const quantity = parseInt(item.querySelector('.quantity-input').value);
            itemCount += quantity;
            subtotal += price * quantity;
        });

        document.getElementById('items-count').textContent = itemCount;
        document.getElementById('subtotal').innerHTML = `<span><img src='images/riyal-removebg-preview.png' style='width:14px;height:14px;'></span> ${subtotal.toFixed(2)} `;
        document.getElementById('total').innerHTML = `<span><img src='images/riyal-removebg-preview.png' style='width:14px;height:14px;'></span> ${subtotal.toFixed(2)} `;
    }

    function incrementQuantity(button) {
        let input = button.previousElementSibling;
        input.value = parseInt(input.value) + 1;
        updateCartTotal();
    }

    function decrementQuantity(button) {
        let input = button.nextElementSibling;
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateCartTotal();
        }
    }

    /*
        function removeItem(button) {
            button.closest('.cart-item').remove();
            updateCartTotal();
        }
    */

    document.addEventListener('DOMContentLoaded', updateCartTotal);
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
