<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['CustID'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The Pet Rock Exchange</title>
<link rel="stylesheet" href="main.css">
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

<!-- Main Content -->
<main>
    <div class="container">
        <h2 class="section-title">Available Pet Rocks</h2>
        <div class="rock-grid" id="rockGrid">
            <!-- Rocks loaded dynamically -->
        </div>
    </div>
</main>

<footer>
&copy; 2025 The Pet Rock Exchange. All rights reserved.
</footer>

<script>
async function loadRocks() {
    try {
        const res = await fetch("php/getrocks.php");
        const rocks = await res.json();
        const grid = document.getElementById("rockGrid");

        if (!rocks || rocks.length === 0) {
            grid.innerHTML = '<p style="text-align:center; font-size:1.2rem;">No rocks available.</p>';
            return;
        }

        rocks.forEach(rock => {
            const card = document.createElement("div");
            card.className = "rock-card";
            card.innerHTML = `
                <img src="assets/images/${rock.PNG_JPG}" alt="${rock.Name}">
                <div class="rock-info">
                    <h3>${rock.Name}</h3>
                    <p><strong>Price:</strong> 💎 ${rock.Price}</p>
                    <button class="buy-button">Buy</button>
                </div>
            `;

            // Buy button redirects to buy.php with rockID
            card.querySelector(".buy-button").addEventListener("click", () => {
                window.location.href = `buy.php?rockID=${rock.RockID}`;
            });

            grid.appendChild(card);
        });
    } catch (err) {
        console.error("Failed to load rocks:", err);
        document.getElementById("rockGrid").innerHTML = '<p style="color:red;">Failed to load rocks.</p>';
    }
}

window.addEventListener("DOMContentLoaded", loadRocks);
</script>
</body>
</html>
