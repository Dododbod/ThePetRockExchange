<?php
session_start();
require "php/db.php";

if (!isset($_SESSION['CustID'])) {
    header("Location: index.php");
    exit;
}

$CustID = $_SESSION['CustID'];

// Fetch user info
$user = $conn->query("SELECT Fname, Lname, Username, ProfilePic FROM customers WHERE CustID = $CustID")->fetch_assoc();

// Fetch user's rocks
$rocks = $conn->query("
    SELECT r.RockID, r.Name, r.RockRarity AS Rarity, r.RockDes AS Description, r.PNG_JPG AS Image, r.Price
    FROM custinv ci
    JOIN rockinfo r ON ci.RockID = r.RockID
    WHERE ci.CustID = $CustID
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Profile | The Pet Rock Exchange</title>
<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="account.css">
</head>
<body>
<nav>
    <div class="nav-container">
        <h1>The Pet Rock Exchange</h1>
        <ul>
            <li><a href="main.php">Buy</a></li>
            <li><a href="sell.php">Sell</a></li>
            <li><a href="trade.php">Trade</a></li>
            <li><a href="account.php" class="active">Account</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </div>
</nav>

<main>
<div class="container profile-container">

    <!-- Profile Header -->
    <div class="profile-header">
        <form id="profilePicForm" enctype="multipart/form-data" method="POST" action="php/upload_profile_pic.php">
            <label for="profilePicInput">
                <img src="assets/images/<?php echo htmlspecialchars($user['ProfilePic'] ?: 'default_profile.jpg'); ?>" alt="User Profile Picture" id="profilePicPreview">
            </label>
            <input type="file" name="profilePic" id="profilePicInput" accept="image/*" style="display:none;" onchange="document.getElementById('profilePicForm').submit();">
        </form>
        <div>
            <h2 id="userFullName"><?php echo htmlspecialchars($user['Fname'].' '.$user['Lname']); ?></h2>
            <p>@<?php echo htmlspecialchars($user['Username']); ?></p>
            <button class="edit-profile-btn" onclick="editProfile()">Edit Name</button>
        </div>
    </div>

    <!-- Editable Name Form (hidden by default) -->
    <div id="editNameForm" style="display:none; margin-bottom:2rem;">
        <input type="text" id="editFname" placeholder="First Name" value="<?php echo htmlspecialchars($user['Fname']); ?>">
        <input type="text" id="editLname" placeholder="Last Name" value="<?php echo htmlspecialchars($user['Lname']); ?>">
        <button onclick="saveName()">Save</button>
        <button onclick="cancelEdit()">Cancel</button>
    </div>

    <!-- Owned Rocks -->
    <div class="owned-rocks">
        <h3>Your Pet Rocks</h3>
        <div class="owned-rocks-grid">
            <?php foreach ($rocks as $rock): ?>
                <div class="owned-rock-card">
                    <img src="assets/images/<?php echo htmlspecialchars($rock['Image']); ?>" alt="<?php echo htmlspecialchars($rock['Name']); ?>">
                    <p class="rock-name"><?php echo htmlspecialchars($rock['Name']); ?></p>
                    <p class="rock-rarity">Rarity: <?php echo htmlspecialchars($rock['Rarity']); ?></p>
                    <p class="rock-desc"><?php echo htmlspecialchars($rock['Description']); ?></p>
                    <p class="rock-price">💎 <?php echo htmlspecialchars($rock['Price']); ?></p>
                </div>
            <?php endforeach; ?>
            <?php if(count($rocks) === 0): ?>
                <p style="grid-column:1/-1;text-align:center;">You have no pet rocks yet!</p>
            <?php endif; ?>
        </div>
    </div>

</div>
</main>

<footer>
    &copy; 2025 The Pet Rock Exchange. All rights reserved.
</footer>

<script>
// Edit Name
function editProfile() {
    document.getElementById('editNameForm').style.display = 'block';
}
function cancelEdit() {
    document.getElementById('editNameForm').style.display = 'none';
}
function saveName() {
    const fname = document.getElementById('editFname').value.trim();
    const lname = document.getElementById('editLname').value.trim();
    if(!fname || !lname) return alert("Enter both first and last name!");

    fetch('php/update_name.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`Fname=${encodeURIComponent(fname)}&Lname=${encodeURIComponent(lname)}`
    }).then(res => res.json()).then(data => {
        if(data.success) {
            document.getElementById('userFullName').textContent = fname + ' ' + lname;
            cancelEdit();
            alert("Name updated!");
        } else alert("Failed to update name: "+(data.error||"Unknown error"));
    });
}
</script>
</body>
</html>
