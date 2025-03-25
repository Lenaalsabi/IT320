<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Invalid request method'); window.history.back();</script>";
    exit();
}

if (!isset($_POST['isbn']) || empty($_POST['isbn'])) {
    echo "<script>alert('Please provide an ISBN'); window.history.back();</script>";
    exit();
}

$isbn = $_POST['isbn'];

$query = "DELETE FROM book WHERE ISBN = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $isbn);

if ($stmt->execute()) {
    echo "<script>alert('Book deleted successfully!'); window.location.href='admin.html';</script>";
} else {
    echo "<script>alert('Failed to delete book.'); window.history.back();</script>";
}
?>
