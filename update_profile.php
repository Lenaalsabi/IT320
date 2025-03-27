<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['customerID'])) {
        header("Location: homepage.html");
        exit();
    }

    $userID = $_SESSION['customerID'];
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phoneNo = trim($_POST['phone_number']);

    $_SESSION['errors'] = []; // Initialize error messages
    $_SESSION['old_firstName'] = $firstName; // Save input for repopulation
    $_SESSION['old_lastName'] = $lastName;
    $_SESSION['old_email'] = $email;
    $_SESSION['old_phoneNo'] = $phoneNo;

    // ✅ Field validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNo)) {
        $_SESSION['errors'][] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors'][] = "Invalid email format.";
    }
    if (!preg_match('/^05\d{8}$/', $phoneNo)) {
        $_SESSION['errors'][] = "Phone number must be 10 digits and start with 05.";
    }

    // ✅ If validation errors exist, reload profile page
    if (!empty($_SESSION['errors'])) {
        header("Location: profile.php");
        exit();
    }

    // ✅ Check if email or phone number already exists for another user
    $checkQuery = "SELECT customerID FROM Customer WHERE (email = ? OR phoneNo = ?) AND customerID != ?";
    $stmt = mysqli_prepare($connection, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $email, $phoneNo, $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['errors'][] = "Email or phone number is already in use.";
        mysqli_stmt_close($stmt);
        header("Location: profile.php");
        exit();
    }
    mysqli_stmt_close($stmt);

    // ✅ Proceed with the update if no duplicate is found
    $updateQuery = "UPDATE Customer SET firstName=?, lastName=?, email=?, phoneNo=? WHERE customerID=?";
    $stmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssssi", $firstName, $lastName, $email, $phoneNo, $userID);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['firstName'] = $firstName;
        $_SESSION['email'] = $email;
    } else {
        $_SESSION['errors'][] = "Failed to update profile. Please try again.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    // ✅ Redirect back to profile page
    header("Location: profile.php");
    exit();
}
?>
