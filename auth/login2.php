<?php
require '../db_connect.php'; // Connect to the database
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: ../login.php");
        exit();
    }

    // CHECK IN ADMIN TABLE
    $adminQuery = "SELECT adminID, password FROM Admin WHERE email = ?";
    $adminStmt = mysqli_prepare($connection, $adminQuery);
    mysqli_stmt_bind_param($adminStmt, "s", $email);
    mysqli_stmt_execute($adminStmt);
    mysqli_stmt_store_result($adminStmt);

    if (mysqli_stmt_num_rows($adminStmt) > 0) {
        mysqli_stmt_bind_result($adminStmt, $adminID, $hashedPassword);
        mysqli_stmt_fetch($adminStmt);

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['adminID'] = $adminID;
            $_SESSION['email'] = $email;
            header("Location: ../admin.html"); 
            exit();
        }
    }
    mysqli_stmt_close($adminStmt);

    // CHECK IN CUSTOMER TABLE
    $customerQuery = "SELECT customerID, firstName, password FROM Customer WHERE email = ?";
    $customerStmt = mysqli_prepare($connection, $customerQuery);
    mysqli_stmt_bind_param($customerStmt, "s", $email);
    mysqli_stmt_execute($customerStmt);
    mysqli_stmt_store_result($customerStmt);

    if (mysqli_stmt_num_rows($customerStmt) > 0) {
        mysqli_stmt_bind_result($customerStmt, $customerID, $firstName, $hashedPassword);
        mysqli_stmt_fetch($customerStmt);

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['customerID'] = $customerID;
            $_SESSION['firstName'] = $firstName;
            $_SESSION['email'] = $email;
            header("Location: ../homebage2.php");
            exit();
        }
    }
    mysqli_stmt_close($customerStmt);
    mysqli_close($connection);

    // INVALID LOGIN ATTEMPT
    $_SESSION['error'] = "Email or password is incorrect.";
    header("Location: ../login.php");
    exit();
} else {
    die("Invalid request method.");
}
?>
