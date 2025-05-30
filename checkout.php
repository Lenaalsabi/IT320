<?php
include 'auth.php';
include 'db_connect.php';
$currentDate = date('Y-m-d'); // Get current date in YYYY-MM-DD format

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - موج</title>

    <link rel="stylesheet" href="stylesBD.css">
    <link rel="stylesheet" href="styles.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Bitter:ital,wght@0,100..900;1,100..900&family=Mate:ital@0;1&family=Poppins&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link href="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css" rel="stylesheet" />
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js"></script>

    <style>
        /* Suggestions box */
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

        /*PB*/

        #progress-bar {
            width: 15%; /* Start at 0 */
            height: 6px;
            border: 1px solid #2a4368;
            background-color: #5281c4;
            border-radius: 10px;
            transition: width 0.5s ease-in-out;
            position: absolute;
            top: 50%; /* Adjust to fit within your step indicator */
            left: 0;
        }

        #progress-track {
            width: 100%; /* Full width */
            border: 1px solid #c1c1c1;
            height: 6px;
            background-color: #e0e0e0; /* Default gray background */
            border-radius: 10px;
            position: absolute;
            top: 50%; /* Adjust if necessary */
            left: 0;
        }


        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: relative; /* Required for the progress bar to be positioned correctly */
            margin-bottom: 20px;
        }


        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            flex: 1; /* This makes all steps evenly spaced */
        }

        .step .circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #c69b50;
            border: 3px solid #e0e0e0;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .step.active .circle {
            background-color: #5281c4;
        }

        .step span {
            font-size: 15px;
            font-weight: bold;
            color: #342721;
            margin-top: 10px;
            position: absolute;
            white-space: nowrap;
            top: -32px;
        }




        /* General Styling for Map Section */
        #map {
            height: 45vh;
            width: 100%;
            align-self: center;
            align-content: center;
            align-items: center;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
        }

        /* Footer */
        footer {
            margin-top: 200px;
            text-align: center;
        }

        .bottom-bar {
            text-align: center;
            margin-top: 20px;
        }

        #submit0 {

            background-color: #fbe7c6;
            color: #342721;
            padding: 10px 12px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            margin: 0 auto;
            width: 130px;
            font-weight: bold;
            font-family: 'Bitter' ,sans-serif;
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

<main>
    <h2 style="margin-top: 30px; text-align: center">Check Out Your Order</h2>
    <div class="container1">

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div id="progress-track"></div>
            <div id="progress-bar" class="progress-bar"></div>
            <div class="step active">
                <div class="circle">1</div>
                <span>Payment</span>
            </div>
            <div class="step">
                <div class="circle">2</div>
                <span>Select map</span>
            </div>
            <div class="step">
                <div class="circle">3</div>
                <span>Confirmation</span>
            </div>
        </div>

        <!-- CARDS -->
        <form method="POST" action="order_process.php">
            <div class="card" id="select-map" style="display: none;">
                <h2>Select your location</h2><br>
                <div id="map"></div>

                <!-- Hidden Inputs for Map Location -->
                <input type="hidden" id="latitude" name="latitude" class="map" required>
                <input type="hidden" id="longitude" name="longitude" class="map" required>
                <div class="button-container">
                    <button type="button" onclick="goToSection('payment-section')">Back</button>
                    <button type="button" id="nextBtn" onclick="validateMap()">Next Step</button><!-- onclick="goToSection('confirmation-section')"-->
                </div>
            </div>

            <!-- Payment Section -->
            <div class="card" id="payment-section">
                <h2>Payment</h2>

                <!--  <input type="hidden" name="total" value="<?php echo htmlspecialchars($_GET['total']); ?>">-->

                <div class="form-group">
                    <label for="card-number">Card Number</label>
                    <input name="card-number" type="text" id="card-number" placeholder="Enter card number" class="input" >
                </div>
                <div class="form-group">
                    <label for="card-name">Name on card</label>
                    <input name="card-name" type="text" id="card-name" placeholder="John Doe" class="input">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input name="cvv" type="text" id="cvv" placeholder="123" class="input">
                </div>
                <div class="form-group">
                    <label for="expiry">Expiry Date</label>
                    <input name="expiry" type="date" id="expiry" min="<?php echo $currentDate; ?>" class="input">
                </div>


                <div class="button-container">
                    <button type="button" id="pay2" >Next Step</button>

                    <!--<button type="button" id="pay2" onclick="goToSection('select-map')">Next Step</button>-->
                </div>
            </div>

            <!-- Confirmation Section -->
            <div class="card" id="confirmation-section" style="display: none;">
                <h2>Confirmation</h2><br>
                <p style="text-align: center;">Your order has been successfully placed!</p>
                <div class="success">
                    <img src="booksDetails/output-onlinegiftools.gif" alt="Checked" style="width:180px;height:180px;">
                    <h2>Order Confirmed!</h2>
                </div>
                <div class="button-container">
                    <input id="submit0" name="pays" style="width: 205px; height: 50px;" type="submit" value="Back to Home Page" onclick="window.location.href='homebage2.php'"><br>
                </div>
            </div>
        </form>

    </div>
</main>

<!-- Footer -->
<footer>
    <div class="footer-section footer-logo">
        <img src="images/logo.png" alt="footer-logo" width="320">
    </div>
    <div class="footer-section social-media">
        <h3>SOCIAL MEDIA</h3>
        <ul class="social-icons">
            <li><a href="#"><img src="images/twitter.png" alt="Twitter"></a></li>
            <li><a href="#"><img src="images/facebook.png" alt="Facebook"></a></li>
            <li><a href="#"><img src="images/instagram.png" alt="Instagram"></a></li>
        </ul>
    </div>
    <div class="footer-section">
        <ul class="terms-conditions">
            <li><a href="terms-conditions.html">Terms & Conditions</a></li>
            <li><a href="privacy-policy.html">Privacy Policy</a></li>
        </ul>
    </div>
</footer>
<script>
    document.getElementById('pay2').addEventListener('click', function() {
        const requiredFields = document.querySelectorAll('.input');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = 'red'; // Highlight empty fields
            } else {
                field.style.borderColor = ''; // Reset border color
            }
        });

        if (!isValid) {
            alert('Please fill in all required fields.');
            return; // Stop the function, do not call goToSection
        }

        goToSection('select-map');
    });
</script>
<script>function validateMap() {
        const latitudeField = document.getElementById('latitude');

        if (!latitudeField.value.trim()) {
            alert('Please select a location on the map.');
            return;
        }

        goToSection('confirmation-section');
    }
</script>

<script>


    // Initialize Mapbox
    mapboxgl.accessToken = 'pk.eyJ1IjoibHVjeTE5MiIsImEiOiJjbTgwOWNzMXowcm1oMmpzYTg4a2F2emNqIn0.f3QS2n20H8D3HpKnOfDdCg';

    const map = new mapboxgl.Map({
        container: "map",
        style: "mapbox://styles/mapbox/streets-v11",
        center: [46.63697443188178,24.723534089769547],//24.723534089769547, 46.63697443188178
        zoom: 11,
    });

    map.addControl(new mapboxgl.GeolocateControl({
        positionOptions:
            {
                enableHighAccuracy:true
            },
        trackUserLocation:true

    }));

    const nav = new mapboxgl.NavigationControl();
    map.addControl(nav, 'top-right');
    map.on('load', () => {
        map.resize(); // Ensures the map adjusts to its container size
    });

    let marker = new mapboxgl.Marker({ draggable: true })
        .setLngLat([46.63697443188178, 24.723534089769547])
        .addTo(map);

    marker.on("dragend", function () {
        const lngLat = marker.getLngLat();
        console.log("Selected Location: ", lngLat);

        updateLocation(lngLat.lng, lngLat.lat);
    });

    function updateLocation(lng, lat) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }


    function goToSection(section) {
        document.querySelectorAll('.card').forEach(function(card) {
            card.style.display = 'none';
        });
        document.getElementById(section).style.display = 'block';
        updateProgress(section);
    }



    function updateProgress(section) {
        let activeStep = 0;

        if (section === 'payment-section') {
            activeStep = 1;
        } else if (section === 'select-map') {
            activeStep = 2;
        } else if (section === 'confirmation-section') {
            activeStep = 3;
        }

        const steps = document.querySelectorAll('.step');
        steps.forEach((step, index) => {
            step.classList.remove('active');
            if (index < activeStep) {
                step.classList.add('active');
            }
        });

        updateProgressBar(activeStep);
    }

    function updateProgressBar(activeStep) {
        let percent = [0, 50, 100]; // Custom progress values
        const progressBar = document.getElementById('progress-bar');

        let progressPercentage = percent[activeStep - 1]; // Adjust index since activeStep starts from 1
        progressBar.style.width = progressPercentage + "%";
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