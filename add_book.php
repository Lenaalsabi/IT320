<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request method!'); window.history.back();</script>";
    exit();
}

$required_fields = ['isbn', 'title', 'author', 'genre', 'price', 'stock_quantity', 'description'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        echo "<script>alert('Missing field: $field'); window.history.back();</script>";
        exit();
    }
}

$isbn = $_POST['isbn'];
$title = $_POST['title'];
$author = $_POST['author'];
$genre = $_POST['genre'];
$price = $_POST['price'];
$stock_quantity = $_POST['stock_quantity'];
$description = $_POST['description'];

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

// 🟢 هنا التعديل الأهم: غيرنا ترتيب نوع المتغيرات
$query = "INSERT INTO book (ISBN, title, Author, Genre, price, stock_quantity, description, cover)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param("ssssddss", $isbn, $title, $author, $genre, $price, $stock_quantity, $description, $cover_filename);

if ($stmt->execute()) {
    echo "<script>alert('Book added successfully!'); window.location.href='admin.html';</script>";
} else {
    echo "<script>alert('Failed to add book'); window.history.back();</script>";
}
?>
