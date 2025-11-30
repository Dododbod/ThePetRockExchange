<?php
session_start();
require "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION['CustID'])) {
    echo json_encode(['success'=>false,'error'=>'Not logged in']);
    exit;
}

$CustID = $_SESSION['CustID'];
$tradeID = $_POST['tradeID'] ?? null;
$action = $_POST['action'] ?? null;

if (!$tradeID || !$action) {
    echo json_encode(['success'=>false,'error'=>'Missing parameters']);
    exit;
}

// Fetch the trade
$stmt = $conn->prepare("SELECT * FROM trades WHERE TradeID=?");
$stmt->bind_param("i", $tradeID);
$stmt->execute();
$result = $stmt->get_result();
$trade = $result->fetch_assoc();

if (!$trade) {
    echo json_encode(['success'=>false,'error'=>'Trade not found']);
    exit;
}

// Only the target/recipient can respond
if ($trade['TargetCustID'] != $CustID) {
    echo json_encode(['success'=>false,'error'=>'You are not authorized to respond to this trade']);
    exit;
}

$conn->begin_transaction();

try {
    if ($action === 'accept') {
        // Swap rocks
        $stmt1 = $conn->prepare("UPDATE custinv SET CustID=? WHERE RockID=? AND CustID=?");
        $stmt1->bind_param("iii", $trade['TargetCustID'], $trade['RequesterRockID'], $trade['RequesterCustID']);
        if (!$stmt1->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("User already owns this rock!");
            } else {
                throw new Exception($conn->error);
            }
        }

        $stmt2 = $conn->prepare("UPDATE custinv SET CustID=? WHERE RockID=? AND CustID=?");
        $stmt2->bind_param("iii", $trade['RequesterCustID'], $trade['TargetRockID'], $trade['TargetCustID']);
        if (!$stmt2->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("User already owns this rock!");
            } else {
                throw new Exception($conn->error);
            }
        }

        // Delete the trade
        $stmtDel = $conn->prepare("DELETE FROM trades WHERE TradeID=?");
        $stmtDel->bind_param("i", $tradeID);
        $stmtDel->execute();

        $conn->commit();
        echo json_encode(['success'=>true,'message'=>'✅ Trade completed! Rocks swapped.']);
        exit;

    } elseif ($action === 'reject') {
        $stmtDel = $conn->prepare("DELETE FROM trades WHERE TradeID=?");
        $stmtDel->bind_param("i", $tradeID);
        $stmtDel->execute();

        $conn->commit();
        echo json_encode(['success'=>true,'message'=>'❌ Trade rejected.']);
        exit;
    }

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
