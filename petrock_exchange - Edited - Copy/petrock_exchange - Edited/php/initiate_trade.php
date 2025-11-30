<?php
session_start();
require "db.php";


header('Content-Type: application/json');

if (!isset($_SESSION['CustID'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$CustID = $_SESSION['CustID'];
$requesterRockID = $_POST['requesterRockID'] ?? null;
$targetRockID = $_POST['targetRockID'] ?? null;
$targetCustID = $_POST['targetCustID'] ?? null;

if (!$requesterRockID || !$targetRockID || !$targetCustID) {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
    exit;
}

// Insert trade
$stmt = $conn->prepare("
    INSERT INTO trades (RequesterCustID, TargetCustID, RequesterRockID, TargetRockID, Status, CreatedAt, UpdatedAt)
    VALUES (?, ?, ?, ?, 'pending', NOW(), NOW())
");
$stmt->bind_param("iiii", $CustID, $targetCustID, $requesterRockID, $targetRockID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
