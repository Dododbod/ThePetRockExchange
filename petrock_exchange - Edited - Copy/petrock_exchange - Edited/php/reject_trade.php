<?php
session_start();
require "db.php";

if (!isset($_SESSION['CustID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$tradeID = $_POST['tradeID'] ?? null;
if (!$tradeID) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing trade ID']);
    exit;
}

// Only target user can reject
$stmt = $conn->prepare("SELECT TargetCustID FROM trades WHERE TradeID=? AND Status='pending'");
$stmt->bind_param("i", $tradeID);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    http_response_code(404);
    echo json_encode(['error' => 'Trade not found']);
    exit;
}

if ($result['TargetCustID'] != $_SESSION['CustID']) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized to reject this trade']);
    exit;
}

// Delete the trade
$stmt = $conn->prepare("DELETE FROM trades WHERE TradeID=?");
$stmt->bind_param("i", $tradeID);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Trade rejected and deleted']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to reject trade']);
}
