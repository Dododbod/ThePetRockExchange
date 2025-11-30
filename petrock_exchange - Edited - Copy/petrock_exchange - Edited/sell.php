<?php
session_start();
require "php/db.php";

// Redirect if not logged in
if (!isset($_SESSION['CustID'])) {
    header("Location: index.php");
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['rockName'] ?? '';
    $desc = $_POST['rockDescription'] ?? '';
    $designer = $_POST['designerName'] ?? '';
    $price = $_POST['rockPrice'] ?? '';
    $rarity = $_POST['rockRarity'] ?? '';

    if (isset($_FILES['rockImage']) && $_FILES['rockImage']['error'] === 0) {
        $ext = pathinfo($_FILES['rockImage']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array(strtolower($ext), $allowed)) {
            $filename = uniqid('rock_') . '.' . $ext;
            $target = 'assets/images/' . $filename;
            if (move_uploaded_file($_FILES['rockImage']['tmp_name'], $target)) {
                $stmt = $conn->prepare("INSERT INTO rockinfo (Price, DateListed, Name, Sold, PNG_JPG, RockDes, RockRarity) VALUES (?, NOW(), ?, 'N', ?, ?, ?)");
                $stmt->bind_param("dsssi", $price, $name, $filename, $desc, $rarity);
                if ($stmt->execute()) {
                    $message = "🪨 Rock listed successfully!";
                } else {
                    $message = "Error inserting rock into database.";
                }
                $stmt->close();
            } else {
                $message = "Failed to upload image.";
            }
        } else {
            $message = "Invalid image type. Only jpg, png, gif allowed.";
        }
    } else {
        $message = "Please select an image to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>List Your Pet Rock | The Pet Rock Exchange</title>
<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="sell.css">
<style>
    .preview {
        margin-top: 20px;
        border: 1px solid #ccc;
        padding: 10px;
        max-width: 300px;
    }
    .preview img {
        max-width: 100%;
        display: block;
        margin-bottom: 10px;
    }
    .preview p {
        margin: 2px 0;
    }
</style>
</head>
<body>

<nav>
    <div class="nav-container">
        <h1>The Pet Rock Exchange</h1>
        <ul>
            <li><a href="main.php">Buy</a></li>
            <li><a href="sell.php" class="active">Sell</a></li>
            <li><a href="trade.php">Trade</a></li>
            <li><a href="account.php">Account</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </div>
</nav>

<main>
    <div class="container sell-container">
        <h2 class="section-title">List Your Pet Rock for Sale</h2>

        <div class="sell-content">
            <div class="listing-form">
                <?php if($message) echo "<p style='color:green;'>$message</p>"; ?>
                <form id="listForm" method="POST" enctype="multipart/form-data">
                    <label for="rockName">Pet Rock Name</label>
                    <input type="text" id="rockName" name="rockName" placeholder="Enter rock's name" required>

                    <label for="rockDescription">Description</label>
                    <textarea id="rockDescription" name="rockDescription" placeholder="Describe your rock" required></textarea>

                    <label for="designerName">Designer Name</label>
                    <input type="text" id="designerName" name="designerName" placeholder="Your name" required>

                    <label for="rockPrice">Price (💎)</label>
                    <input type="number" step="0.01" id="rockPrice" name="rockPrice" placeholder="Enter price" required>

                    <label for="rockRarity">Rarity (1-5)</label>
                    <input type="number" id="rockRarity" name="rockRarity" min="1" max="5" required>

                    <label for="rockImage">Upload Image</label>
                    <input type="file" id="rockImage" name="rockImage" accept="image/*" required>

                    <button type="submit" class="list-btn">List Pet Rock</button>
                </form>
            </div>

            <!-- Preview Section -->
            <div class="preview" id="rockPreview" style="display:none;">
                <img id="previewImage" src="" alt="Rock Preview">
                <p><strong>Name:</strong> <span id="previewName"></span></p>
                <p><strong>Description:</strong> <span id="previewDesc"></span></p>
                <p><strong>Designer:</strong> <span id="previewDesigner"></span></p>
                <p><strong>Price:</strong> 💎 <span id="previewPrice"></span></p>
                <p><strong>Rarity:</strong> <span id="previewRarity"></span></p>
            </div>
        </div>
    </div>
</main>

<footer>
&copy; 2025 The Pet Rock Exchange. All rights reserved.
</footer>

<script>
const form = document.getElementById("listForm");
const preview = document.getElementById("rockPreview");
const previewImage = document.getElementById("previewImage");
const previewName = document.getElementById("previewName");
const previewDesc = document.getElementById("previewDesc");
const previewDesigner = document.getElementById("previewDesigner");
const previewPrice = document.getElementById("previewPrice");
const previewRarity = document.getElementById("previewRarity");

// Update preview on input
function updatePreview() {
    const name = document.getElementById("rockName").value;
    const desc = document.getElementById("rockDescription").value;
    const designer = document.getElementById("designerName").value;
    const price = document.getElementById("rockPrice").value;
    const rarity = document.getElementById("rockRarity").value;
    const file = document.getElementById("rockImage").files[0];

    previewName.textContent = name;
    previewDesc.textContent = desc;
    previewDesigner.textContent = designer;
    previewPrice.textContent = price;
    previewRarity.textContent = rarity;

    if (file) {
        const reader = new FileReader();
        reader.onload = e => previewImage.src = e.target.result;
        reader.readAsDataURL(file);
    }

    preview.style.display = name || desc || designer || price || rarity || file ? "block" : "none";
}

// Event listeners
document.getElementById("rockName").addEventListener("input", updatePreview);
document.getElementById("rockDescription").addEventListener("input", updatePreview);
document.getElementById("designerName").addEventListener("input", updatePreview);
document.getElementById("rockPrice").addEventListener("input", updatePreview);
document.getElementById("rockRarity").addEventListener("input", updatePreview);
document.getElementById("rockImage").addEventListener("change", updatePreview);
</script>
</body>
</html>
