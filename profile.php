<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['customerID'])) {
    header("Location: homepage.html");
    exit();
}

$userID = $_SESSION['customerID'];
$query = "SELECT firstName, lastName, email, phoneNo FROM Customer WHERE customerID = ?";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userData = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
   <style> 
   .error-messages {
            color: red;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
    <!DOCTYPE html>   
    <body>
        <header>
            <div class="header">
                <div class="logo-section">
                    <div class="horizontal-line"></div>
                    <div class="logo">
                        <a href="homebage2.php">
                            <img src="images/logo.png" alt=" موج Logo" id="logo">
                        </a>
                    </div>
                    <div class="horizontal-line"></div>
                </div>
                <nav id="nav">
                    <ul>
                        <li><a class="button primary small signup-btn" href="auth/logout.php">Log out</a></li>
                    </ul>
                </nav>
                <nav class="link-section">
                    <div class="icons">
                        <a href="wishlist.php"><img src="images/love.png" alt="Wishlist"><p>Wishlist</p></a>
                        <a href="cart.php"><img src="images/cart.png" alt="Cart"><p>Cart</p></a>
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
                        <a href="books.php"><img src="images/books.png" alt="Books"><p>Books</p></a>
                    </div>
                </nav>
            </div>
        </header>
        <section class="profile-container">
            <h2>Profile</h2>
            <div class="profile-card">
                <img src="images/user.png" alt="User Profile Picture" class="profile-pic" id="profileImage">
                <?php if (!empty($_SESSION['errors'])): ?>
    <div class="error-messages">
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['errors']); // Clear errors after displaying ?>
<?php endif; ?>

                <form action="update_profile.php" method="post">
                <div class="form-group">
                    <label for="first_name">First name:</label>
                    <div class="input-wrapper">
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($userData['firstName']) ?>" readonly>
                    <span class="edit-icon" onclick="toggleEdit('first_name')">✎</span>
                </div>
                </div>

                <div class="form-group">
                    <label for="last_name">Last name:</label>
                    <div class="input-wrapper">
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($userData['lastName']) ?>" readonly>
                    <span class="edit-icon" onclick="toggleEdit('last_name')">✎</span>
                </div>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" readonly>
                    <span class="edit-icon" onclick="toggleEdit('email')">✎</span>
                </div>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone number:</label>
                    <div class="input-wrapper">
                    <input type="tel" id="phone_number" name="phone_number" value="<?= htmlspecialchars($userData['phoneNo']) ?>" readonly>
                    <span class="edit-icon" onclick="toggleEdit('phone_number')">✎</span>
                </div>
                </div>

                <button type="submit" id="save-button" class="update-profile-btn" style="display:none;">Update Profile</button>
            </form>
            </div>
        </section>
    
    
        <script>

    
            function toggleEdit(fieldId) {
                const inputField = document.getElementById(fieldId);
                const editIcon = document.querySelector(`#${fieldId} + .edit-icon`);
                const saveButton = document.getElementById('save-button');
                
                // Toggle read-only state
                if (inputField.readOnly) {
                    inputField.readOnly = false;
                    editIcon.style.display = 'none'; 
                    saveButton.style.display = 'inline-block'; 
                } else {
                    inputField.readOnly = true;
                    editIcon.style.display = 'inline'; 
                    saveButton.style.display = 'none'; 
                }
            }
        </script>

    </body>
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
              </html>