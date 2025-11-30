<?php
session_start();
require "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['CustID'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$CustID = $_SESSION['CustID'];
$Fname = trim($_POST['Fname'] ?? '');
$Lname = trim($_POST['Lname'] ?? '');

if (!$Fname || !$Lname) {
    echo json_encode(['success' => false, 'error' => 'Both first and last names are required']);
    exit;
}

$stmt = $conn->prepare("UPDATE customers SET Fname=?, Lname=? WHERE CustID=?");
$stmt->bind_param("ssi", $Fname, $Lname, $CustID);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Name updated']);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
