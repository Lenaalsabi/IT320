<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        .signup-container {
            display: flex;
            justify-content: center;    
            align-items: center;
            height: 80vh;
            margin-top: 170px;
            margin-bottom: 170px;
        }
        
        .signup-box {
            background-color: #FFFCF5;
            padding: 2.5em;
            border-radius: 0.625em;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 35em;
        }

        .signup-box h2 {
            font-family: 'Bitter', sans-serif;
            color: #2f231e;
            margin-bottom: 1em;
        }
        
        .signup-box label {
            display: block;
            text-align: left;
            font-family: 'Bitter', sans-serif;
            font-size: 0.9em;
            color: #2f231e;
            margin-bottom: 0.3em;
        }
        
        .signup-box input {
            width: 100%;
            padding: 0.75em;
            margin-bottom: 1em;
            border: 1px solid #d1c4b6;
            border-radius: 0.5em;
            background-color: #f4f2ed;
            font-family: 'Bitter', sans-serif;
        }
        
        .signup-box input:focus {
            border-color: #F8D49D;
            outline: none;
        }

        .error-message {
            color: red;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <header>
        <div class="header">
            <div class="logo-section">
                <div class="horizontal-line"></div>
                <div class="logo">
                    <a href="homepage.html">
                        <img src="images/logo.png" alt="موج Logo" id="logo">
                    </a>
                </div>
                <div class="horizontal-line"></div>
            </div>
        </div>
    </header>

    <main>
        <div class="signup-container">
            <div class="signup-box">
                <h2>Sign Up</h2>

                <!-- Display Errors -->
                <?php if (!empty($_SESSION['errors'])): ?>
                    <div class="error-message">
                        <?php 
                            foreach ($_SESSION['errors'] as $error) {
                                echo "<p>$error</p>";
                            }
                            unset($_SESSION['errors']); // Clear errors after displaying
                        ?>
                    </div>
                <?php endif; ?>

                <form action="auth/signup2.php" method="POST">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" value="<?= isset($_SESSION['old_firstName']) ? htmlspecialchars($_SESSION['old_firstName']) : '' ?>" required>
                    
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" value="<?= isset($_SESSION['old_lastName']) ? htmlspecialchars($_SESSION['old_lastName']) : '' ?>" required>
                
                    <label for="phoneNo">Phone Number</label>
                    <input type="tel" id="phoneNo" name="phoneNo" placeholder="Enter your phone number" value="<?= isset($_SESSION['old_phoneNo']) ? htmlspecialchars($_SESSION['old_phoneNo']) : '' ?>" required>
                
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= isset($_SESSION['old_email']) ? htmlspecialchars($_SESSION['old_email']) : '' ?>" required>
                
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                
                    <button type="submit" class="button primary">Sign Up</button>
                </form>

                <p class="signup-text">Already have an account? <a href="login.php">Log In</a></p>
            </div>
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
        <p>Terms and Conditions | Privacy Policy<br>&copy; 2024 Mawj Company. All rights reserved.</p>
    </div>
</body>
</html>
