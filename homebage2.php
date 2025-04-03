<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mawj - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap" rel="stylesheet">
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
    
       

    <div class="image-container">
        <img src="images/bbook.jpg" alt="Background Image">
        <div class="image-text">
            Books are doors to endless adventures, unlocking worlds of imagination,<br> wonder, and timeless knowledge.
        </div>
    </div>
    <div class="user-guide-container">
        <div class="icon-container" onclick="openPopup()">
            <img src="images/gg.png" alt="User Guide">
            <p>User Guide</p>
        </div>
    </div>

    <div id="popup" class="popup-overlay">
        <div class="popup-content">
            <h2>User Guide</h2>
            <pre><strong>How to borrow:</strong> 
                1. Select the desired book.  
                2. Click the "Borrow" button.  
                3. Fill out the reservation form (start date, end date, delivery location).  
                4. Submit the request and wait for confirmation.  
                5. Receive the book at the specified location.  
            </pre>
            <pre><strong>How to return:</strong> 
                1. Go to the "Orders" section.  
                2. Select the book you want to return.  
                3. Click the "Edit"button and follow the instructions for the return location or pickup service.</pre>
            <button onclick="closePopup()">Close</button>
        </div>
    </div>
   <section class="offers-section">
    <h2>Affordable Books</h2>
    <div class="books2-container">
        <?php
        include 'db_connect.php';

        $query = "SELECT ISBN, title, cover, price FROM book WHERE price < 40";
        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <div class="book" onclick="window.location.href='book_details.php?isbn=<?php echo $row['ISBN']; ?>'">
                    <img src="uploads/<?php echo $row['cover']; ?>" alt="<?php echo $row['title']; ?>">
                    <p style="font-family: 'Bitter', serif; font-weight: bold; margin-top: 10px;"><?php echo $row['title']; ?></p>
                    <p style="font-family: 'Bitter', serif; color: #988414; font-size: 15px;"><?php echo $row['price']; ?> SAR</p>
                </div>
        <?php
            }
        } else {
            echo "<p style='text-align:center;'>No affordable books available right now.</p>";
        }
        ?>
    </div>
</section>


   
    
      
    
     
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
       <div class="bottom-bar">
                  <p>  Terms and Conditions
      privacy policy<br>&copy; 2024 mawj company . All rights reserved</p>
              </div>
            </body>
             
    <script>
const popularTab = document.getElementById("popular-tab");
const mostTab = document.getElementById("most-tab");

const popularSection = document.getElementById("popular-section");
const mostSection = document.getElementById("most-section");

popularTab.addEventListener("click", () => {
    showSection(popularTab, popularSection);
});

mostTab.addEventListener("click", () => {
    showSection(mostTab, mostSection);
});

function showSection(activeTab, activeSection) {
    popularSection.style.display = "none";
    mostSection.style.display = "none";

    popularTab.classList.remove("active");
    mostTab.classList.remove("active");

    activeSection.style.display = "block";
    activeTab.classList.add("active");
}

showSection(popularTab, popularSection);
function openPopup() {
    document.getElementById("popup").style.display = "flex";
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}

window.onclick = function(event) {
    let popup = document.getElementById("popup");
    if (event.target === popup) {
        popup.style.display = "none";
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
              </html>
              
              
          