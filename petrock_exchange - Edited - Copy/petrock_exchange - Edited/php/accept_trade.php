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

// Get trade info
$stmt = $conn->prepare("SELECT * FROM trades WHERE TradeID=? AND Status='pending'");
$stmt->bind_param("i", $tradeID);
$stmt->execute();
$trade = $stmt->get_result()->fetch_assoc();

if (!$trade) {
    http_response_code(404);
    echo json_encode(['error' => 'Trade not found']);
    exit;
}

// Only target user can accept
if ($trade['TargetCustID'] != $_SESSION['CustID']) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized to accept this trade']);
    exit;
}

// Swap rocks
$conn->begin_transaction();

try {
    // Give target rock to requester
    $stmt = $conn->prepare("UPDATE custinv SET CustID=? WHERE RockID=?");
    $stmt->bind_param("ii", $trade['RequesterCustID'], $trade['TargetRockID']);
    $stmt->execute();

    // Give requester rock to target
    $stmt = $conn->prepare("UPDATE custinv SET CustID=? WHERE RockID=?");
    $stmt->bind_param("ii", $trade['TargetCustID'], $trade['RequesterRockID']);
    $stmt->execute();

    // Update trade status
    $stmt = $conn->prepare("UPDATE trades SET Status='accepted', UpdatedAt=NOW() WHERE TradeID=?");
    $stmt->bind_param("i", $tradeID);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Trade completed']);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Trade failed', 'details' => $e->getMessage()]);
}
