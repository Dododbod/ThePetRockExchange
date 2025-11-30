<?php
header("Content-Type: application/json");
require "db.php";
session_start();

if (!isset($_SESSION['CustID'])) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT RockID, Name, Price, PNG_JPG FROM rockinfo";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$rocks = [];
while ($row = $result->fetch_assoc()) {
    $rocks[] = $row;
}

echo json_encode($rocks);

$stmt->close();
$conn->close();
