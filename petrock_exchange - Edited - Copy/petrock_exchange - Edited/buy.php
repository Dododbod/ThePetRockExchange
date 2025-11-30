<?php
session_start();
require "php/db.php";

// Redirect if not logged in
if (!isset($_SESSION['CustID'])) {
    header("Location: index.php");
    exit;
}

$CustID = $_SESSION['CustID'];

// Get rockID from query
$rockID = $_GET['rockID'] ?? null;
if (!$rockID) {
    die("No rock selected.");
}

// Fetch rock info from DB
$stmt = $conn->prepare("SELECT Name, Price, RockDes, RockRarity, PNG_JPG FROM rockinfo WHERE RockID=?");
$stmt->bind_param("i", $rockID);
$stmt->execute();
$result = $stmt->get_result();
$rock = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$rock) {
    die("Rock not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buy <?php echo htmlspecialchars($rock['Name']); ?> | The Pet Rock Exchange</title>
<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="buy.css">
</head>
<body>

<!-- Navigation -->
<nav>
    <div class="nav-container">
        <h1>The Pet Rock Exchange</h1>
        <ul>
            <li><a href="main.php">Buy</a></li>
            <li><a href="sell.php">Sell</a></li>
            <li><a href="trade.php">Trade</a></li>
            <li><a href="account.php">Account</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </div>
</nav>

<main>
    <div class="container purchase-container">
        <h2 class="section-title">Confirm Your Pet Rock Purchase</h2>
        <div class="purchase-content">

            <!-- Rock Preview -->
            <div class="rock-preview">
                <img src="assets/images/<?php echo htmlspecialchars($rock['PNG_JPG']); ?>" 
                     alt="<?php echo htmlspecialchars($rock['Name']); ?>">
                <div class="price-info">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($rock['Name']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($rock['RockDes']); ?></p>
                    <p><strong>Rarity:</strong> <?php echo htmlspecialchars($rock['RockRarity']); ?></p>
                    <p><strong>Price:</strong> 💎 <span id="rockPrice"><?php echo $rock['Price']; ?></span></p>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="payment-form">
                <h3>Payment Details</h3>
                <form id="purchaseForm">
                    <label for="cardName">Name on Card</label>
                    <input type="text" id="cardName" placeholder="Alex Stonekeeper" required>

                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" maxlength="19" placeholder="1234 5678 9012 3456" required>

                    <label for="expiration">Expiration Date</label>
                    <input type="month" id="expiration" required>

                    <label for="ccv">CCV</label>
                    <input type="text" id="ccv" maxlength="4" placeholder="123" required>

                    <label for="billingAddress">Zip Code</label>
                    <input type="text" id="billingAddress" placeholder="12345" required>

                    <button type="submit" class="confirm-btn">Confirm Purchase</button>
                </form>
            </div>

        </div>
    </div>
</main>

<footer>
&copy; 2025 The Pet Rock Exchange. All rights reserved.
</footer>

<!-- Autofill card info & handle confirm purchase -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const cardName = document.getElementById("cardName");
    if (!cardName) return; // Only run on buy.php

    // Autofill saved card info
    fetch("php/getCardInfo.php")
        .then(res => res.json())
        .then(data => {
            if (!data || Object.keys(data).length === 0) return;

            document.getElementById("cardName").value = data.CardName || "";
            document.getElementById("cardNumber").value = data.CardNum || "";

            let exp = data.ExpDate || "";
            if (exp.includes("/")) {
                let [mm, yy] = exp.split("/");
                exp = `20${yy}-${mm}`;
            }
            document.getElementById("expiration").value = exp;
            document.getElementById("ccv").value = data.CCV || "";
            document.getElementById("billingAddress").value = data.BillingZip || "";
        })
        .catch(err => console.error("Fetch error:", err));

    // Handle Confirm Purchase
    const purchaseForm = document.getElementById("purchaseForm");
    purchaseForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const purchaseData = {
            CardNum: document.getElementById("cardNumber").value.trim(),
            CardName: document.getElementById("cardName").value.trim(),
            ExpDate: document.getElementById("expiration").value,
            CCV: document.getElementById("ccv").value.trim(),
            BillingZip: document.getElementById("billingAddress").value.trim(),
            RockID: <?php echo $rockID; ?>
        };

        try {
            const res = await fetch("php/confirmPurchase.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(purchaseData)
            });

            const result = await res.json();

            if (result.success) {
                alert(result.message);
                window.location.href = "main.php";
            } else {
                alert("Error: " + result.message);
            }
        } catch (err) {
            console.error(err);
            alert("Purchase failed due to server error.");
        }
    });
});
</script>

</body>
</html>
