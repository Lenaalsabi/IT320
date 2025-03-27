<?php
require '../db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $phoneNo = trim($_POST['phoneNo']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    $_SESSION['errors'] = []; // Initialize error messages
    $_SESSION['old_firstName'] = $firstName; // Save form values for repopulation
    $_SESSION['old_lastName'] = $lastName;
    $_SESSION['old_phoneNo'] = $phoneNo;
    $_SESSION['old_email'] = $email;

    // ✅ Field validation
    if (empty($firstName) || empty($lastName) || empty($phoneNo) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['errors'][] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors'][] = "Invalid email format.";
    }
    if ($password !== $confirmPassword) {
        $_SESSION['errors'][] = "Passwords do not match.";
    }
    if (!preg_match('/^05\d{8}$/', $phoneNo)) {
        $_SESSION['errors'][] = "Phone number must be 10 digits and start with 05.";
    }

    // ✅ If there are validation errors, go back to signup.php
    if (!empty($_SESSION['errors'])) {
        header("Location: ../signup.php");
        exit(); // Stop execution
    }

    // ✅ Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Check if email or phone already exists
    $checkQuery = "SELECT customerID FROM Customer WHERE email = ? OR phoneNo = ?";
    $stmt = mysqli_prepare($connection, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ss", $email, $phoneNo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['errors'][] = "Email or phone number already in use.";
        mysqli_stmt_close($stmt); // ✅ Close before redirect
        header("Location: ../signup.php");
        exit();
    }
    mysqli_stmt_close($stmt); // Close before inserting

    // ✅ Insert user
    $query = "INSERT INTO Customer (firstName, lastName, phoneNo, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $phoneNo, $email, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['customerID'] = mysqli_insert_id($connection);
        $_SESSION['firstName'] = $firstName;
        $_SESSION['email'] = $email;
    
        // ✅ Unset old form data since signup was successful
        unset($_SESSION['old_firstName']);
        unset($_SESSION['old_lastName']);
        unset($_SESSION['old_phoneNo']);
        unset($_SESSION['old_email']);
    
        mysqli_stmt_close($stmt); // ✅ Close before redirect
        mysqli_close($connection); // ✅ Close DB connection
        header("Location: ../homebage2.php");
        exit();
    }
  else {
        $_SESSION['errors'][] = "Something went wrong. Please try again.";
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        header("Location: ../signup.php");
        exit();
    }
}
else {
    die("Invalid request method.");
}

?>
