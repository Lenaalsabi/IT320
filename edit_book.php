<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request method'); window.history.back();</script>";
    exit();
}

if (!isset($_POST['isbn']) || empty($_POST['isbn'])) {
    echo "<script>alert('Please enter the ISBN'); window.history.back();</script>";
    exit();
}

$isbn = $_POST['isbn'];
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'];
$description = $_POST['description'];

// cover (optional)
$cover_filename = null;
if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $cover_filename = basename($_FILES['cover']['name']);
    $target_path = $upload_dir . $cover_filename;

    if (!move_uploaded_file($_FILES['cover']['tmp_name'], $target_path)) {
        echo "<script>alert('Failed to upload cover image'); window.history.back();</script>";
        exit();
    }
}

// SQL
if ($cover_filename) {
    $query = "UPDATE book SET price = ?, stock_quantity = ?, description = ?, cover = ? WHERE ISBN = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("disss", $price, $stock_quantity, $description, $cover_filename, $isbn);
} else {
    $query = "UPDATE book SET price = ?, stock_quantity = ?, description = ? WHERE ISBN = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("diss", $price, $stock_quantity, $description, $isbn);
}

if ($stmt->execute()) {
    echo "<script>alert('Book updated successfully!'); window.location.href='admin.html';</script>";
} else {
    echo "<script>alert('Failed to update book.'); window.history.back();</script>";
}
?>
