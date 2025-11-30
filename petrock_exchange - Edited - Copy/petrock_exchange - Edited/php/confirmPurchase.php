<?php
header("Content-Type: application/json");
session_start();
require "db.php";

// Check if user is logged in
if (!isset($_SESSION['CustID'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$CustID = $_SESSION['CustID'];

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$CardNum = $data['CardNum'] ?? null;
$CardName = $data['CardName'] ?? null;
$ExpDate = $data['ExpDate'] ?? null; // YYYY-MM
$CCV = $data['CCV'] ?? null;
$BillingZip = $data['BillingZip'] ?? null;
$RockID = $data['RockID'] ?? null;

if (!$CardNum || !$CardName || !$ExpDate || !$CCV || !$BillingZip || !$RockID) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Convert expiration date to MM/YY for storage
if (strpos($ExpDate, "-") !== false) {
    [$yyyy, $mm] = explode("-", $ExpDate);
    $ExpDateDB = sprintf("%02d/%02d", $mm, substr($yyyy, 2, 2));
} else {
    $ExpDateDB = $ExpDate;
}

// 1️⃣ Start transaction
$conn->begin_transaction();

try {
    // 2️⃣ Insert card into `card` table if it doesn't exist
    $stmt = $conn->prepare("SELECT CardNum FROM card WHERE CardNum = ?");
    $stmt->bind_param("s", $CardNum);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO card (CardNum, Name, ExpDate, CCV, Deleted) VALUES (?, ?, ?, ?, 'N')");
        $stmt->bind_param("ssss", $CardNum, $CardName, $ExpDateDB, $CCV);
        $stmt->execute();
    }
    $stmt->close();

    // 3️⃣ Insert into custcard if not exists
    $stmt = $conn->prepare("SELECT * FROM custcard WHERE CustID = ? AND CardNum = ?");
    $stmt->bind_param("is", $CustID, $CardNum);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO custcard (CustID, CardNum, BillingZip, Deleted) VALUES (?, ?, ?, 'N')");
        $stmt->bind_param("iss", $CustID, $CardNum, $BillingZip);
        $stmt->execute();
    }
    $stmt->close();

    // 4️⃣ Insert purchased rock into custinv
    $stmt = $conn->prepare("INSERT INTO custinv (CustID, RockID) VALUES (?, ?)");
    $stmt->bind_param("ii", $CustID, $RockID);
    $stmt->execute();
    $stmt->close();

    // 5️⃣ Commit transaction
    $conn->commit();
    $conn->close();

    echo json_encode(["success" => true, "message" => "Purchase successful!"]);

} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    echo json_encode(["success" => false, "message" => "Purchase failed: " . $e->getMessage()]);
}
?>
