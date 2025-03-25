<?php
include 'db_connect.php';

header('Content-Type: application/json');

$query = isset($_GET['query']) ? $_GET['query'] : '';
$search = "%" . $query . "%";

$sql = "SELECT ISBN, title FROM book WHERE title LIKE ? LIMIT 10";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row;
}

echo json_encode($suggestions);
