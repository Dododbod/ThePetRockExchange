<?php
header("Content-Type: application/json");
require "db.php";
session_start();

// Must be logged in
if (!isset($_SESSION["CustID"])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$CustID = $_SESSION["CustID"];

// Query to join custcard and card table
$sql = "
    SELECT 
        custcard.CardNum,
        custcard.BillingZip,
        card.Name AS CardName,
        card.ExpDate,
        card.CCV
    FROM custcard
    LEFT JOIN card ON custcard.CardNum = card.CardNum
    WHERE custcard.CustID = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $CustID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([]); // No saved card
}

$stmt->close();
$conn->close();
?>
