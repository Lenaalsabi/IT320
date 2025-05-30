<?php
session_start();

if (!isset($_SESSION['adminID'])) {
    header("Location: homepage.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - موج</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@100..900&display=swap" rel="stylesheet">
   
    
</head>
<body>
    <header>
        <div class="header">
            <div class="logo-section">
                <div class="horizontal-line"></div>
                <div class="logo">
                    
                        <img src="images/logo.png" alt="موج Logo" id="logo">
                    
                </div>
                <div class="horizontal-line"></div>
            </div>
            <nav id="nav">
                <ul>
                    <li><a class="button primary small signup-btn" href="auth/logout.php">Log out</a></li>
                
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
    <div class="admin-dashboard-container">
        <div class="title-section">
            <div class="horizontal-line"></div>
            <div class="title">
                <h1 class="page-title">admin<br> Dashboard</h1>
            </div>
            <div class="horizontal-line"></div>
        </div>

        
        <div class="admin-menu">
            <div class="admin-option" onclick="showSection('add-book')">
                <img src="images/addd.png" alt="Add Book">
                <p>Add Book</p>
            </div>
            <div class="admin-option" onclick="showSection('edit-book')">
                <img src="images/editt.png" alt="Edit Book">
                <p>Edit Book</p>
            </div>
            <div class="admin-option" onclick="showSection('delete-book')">
                <img src="images/del.png" alt="Delete Book">
                <p>Delete Book</p>
            </div>
        </div>

        <div class="admin-content">
            <section id="add-book" class="admin-section hidden">
                <h2>Add a New Book</h2>
                <form action="add_book.php" method="POST" enctype="multipart/form-data">
     <input type="text" name="isbn" placeholder="ISBN" required>             
    <input type="text" name="title" placeholder="Book Title" required>
    <input type="number" name="stock_quantity" placeholder="Quantity" required>
    <input type="text" name="author" placeholder="Author" required>
    <input type="text" name="genre" placeholder="Genre" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="number" name="price" placeholder="Price" required>
    <label for="cover-page">Add Cover Page:</label>
    <input type="file" id="cover-page" name="cover" accept="image/*" required>
    <button type="submit">Add Book</button>

                </form>
            </section>

            <section id="edit-book" class="admin-section hidden">
    <h2>Edit Book</h2>

    <form action="edit_book.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="isbn" placeholder="Enter ISBN" required>
        <input type="number" name="price" placeholder="New Price" required>
        <input type="number" name="stock_quantity" placeholder="New Stock Quantity" required>
        <textarea name="description" placeholder="New Description" required></textarea>
        <label for="cover">Update Cover:</label>
        <input type="file" name="cover" accept="image/*">
        <button type="submit">Save Changes</button>
    </form>
</section>


           <section id="delete-book" class="admin-section hidden">
    <h2>Delete a Book</h2>
    <form action="delete_book.php" method="POST">
        <input type="text" name="isbn" placeholder="Enter ISBN to delete" required>
        <button type="submit">Delete Book</button>
    </form>
</section>

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
        <p>  Terms and Conditions
privacy policy<br>&copy; 2024 mawj company . All rights reserved</p>
    </div>
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.admin-section').forEach(section => {
                section.classList.add('hidden');
                section.classList.remove('active');
            });
            
            let activeSection = document.getElementById(sectionId);
            if (activeSection) {
                activeSection.classList.remove('hidden');
                activeSection.classList.add('active');
            }
        }
    </script>
</body>
</html>
