<?php
include 'auth.php';
include 'db_connect.php';



if (!isset($_GET['title'])) {
    echo "<label>Book not found.</label>";
    exit();
}

$title = $_GET['title'];
$query = "SELECT * FROM book WHERE title = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<label>Book not found.</label>";
    exit();
}

$book = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        .borrowing-container {
            padding: 30px;
            background-color:  #FFFCF5;
            max-width: 600px;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        .borrowing-form ,input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 0.75em;
            margin-bottom: 1em;
            border: 1px solid #d1c4b6;
            border-radius: 0.5em;
            background-color: #f4f2ed;
            font-family: 'Bitter', sans-serif;
        }

        .button-group {
            text-align: center;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin: 5px;
        }

        .primary {
            background-color: #4CAF50;
            color: white;
        }

        .cancel {
            background-color: #f44336;
            color: white;
        }

        .edit {
            background-color: #ffa500;
            color: white;
        }
        .booking-details {
            margin-top: 60px;
            margin-bottom: 60px;
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

        }</style>
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


<div class="borrowing-container">
    <h2>Book Borrowing</h2>

    <form id="borrowing-form"  action="order_items.php" method="POST">

        <input type="hidden" name="ISBN" value="<?php echo $book['ISBN']; ?>">

        <div class="form-group">
            <label for="book-title">Book Title:</label>
            <input type="text" id="book-title" name="book-title" readonly value="<?php echo htmlspecialchars($title); ?>">
        </div>

        <div class="form-group">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date" name="start-date" required>
        </div>

        <div class="form-group">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date" name="end-date" required>
        </div>


        <div class="button-group">
            <button type="submit" name="booking" id="create-reservation" class="button primary">Create Reservation</button>
        </div>
    </form>

    <div class="booking-details" style="display: none;">
        <h3>Booking Information</h3>
        <p id="booking-summary"></p>

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
    <p>Terms and Conditions | Privacy Policy<br>&copy; 2024 Mawj Company. All rights reserved.</p>
</div>




<script>

    /*

         document.getElementById('borrowing-form').addEventListener('submit', function(event) {
             event.preventDefault();

             const bookTitle = document.getElementById('book-title').value;
             const startDate = new Date(document.getElementById('start-date').value);
             const endDate = new Date(document.getElementById('end-date').value);
             const pricePerDay = 2;

             if (endDate <= startDate) {
                 alert("End date must be after start date!");
                 return;
             }

             const daysDifference = (endDate - startDate) / (1000 * 3600 * 24);
             const totalPrice = daysDifference * pricePerDay;

             document.getElementById('borrowing-form').style.display = 'none';
             document.querySelector('.booking-details').style.display = 'block';

             document.getElementById('booking-summary').innerHTML = `
                 <strong>Book Title:</strong> ${bookTitle} <br>
                 <strong>Start Date:</strong> ${startDate.toDateString()} <br>
                 <strong>End Date:</strong> ${endDate.toDateString()} <br>
                 <strong>Total Price:</strong> ${totalPrice} SAR.
             `;

             // Submit the form after displaying details
             this.submit();
         });
         */
</script>

</body>


</html>

