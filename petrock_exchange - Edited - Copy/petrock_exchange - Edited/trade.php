<?php
session_start();
require "php/db.php";

if (!isset($_SESSION['CustID'])) {
    header("Location: index.php");
    exit;
}

$CustID = $_SESSION['CustID'];

// Optional: target user from URL
$targetUserID = $_GET['targetCustID'] ?? null;
$targetUserName = $_GET['targetUserName'] ?? null;

// Fetch your rocks
$yourRocks = $conn->query("
    SELECT ci.RockID, r.Name, r.Price, r.PNG_JPG AS Image
    FROM custinv ci
    JOIN rockinfo r ON ci.RockID = r.RockID
    WHERE ci.CustID = $CustID
")->fetch_all(MYSQLI_ASSOC);

// Fetch target user's rocks if user selected
$targetRocks = [];
if ($targetUserID) {
    $targetRocks = $conn->query("
        SELECT ci.RockID, r.Name, r.Price, r.PNG_JPG AS Image
        FROM custinv ci
        JOIN rockinfo r ON ci.RockID = r.RockID
        WHERE ci.CustID = $targetUserID
    ")->fetch_all(MYSQLI_ASSOC);
}

// Fetch pending trade
$pendingTrade = $conn->query("
    SELECT t.*, 
           r1.Name AS requesterRockName, r1.PNG_JPG AS requesterRockImg, r1.Price AS requesterRockPrice,
           r2.Name AS targetRockName, r2.PNG_JPG AS targetRockImg, r2.Price AS targetRockPrice,
           c.Fname, c.Lname
    FROM trades t
    JOIN rockinfo r1 ON t.RequesterRockID = r1.RockID
    JOIN rockinfo r2 ON t.TargetRockID = r2.RockID
    JOIN customers c ON t.RequesterCustID = c.CustID
    WHERE (t.TargetCustID = $CustID OR t.RequesterCustID = $CustID) AND t.Status LIKE 'pending%'
    ORDER BY t.CreatedAt DESC
    LIMIT 1
")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trade Pet Rocks | The Pet Rock Exchange</title>
<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="trade.css">
</head>
<body>
<nav>
    <div class="nav-container">
        <h1>The Pet Rock Exchange</h1>
        <ul>
            <li><a href="main.php">Buy</a></li>
            <li><a href="sell.php">Sell</a></li>
            <li><a href="trade.php" class="active">Trade</a></li>
            <li><a href="account.php">Account</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </div>
</nav>

<main>
<div class="container trade-container">

<?php if ($pendingTrade): ?>
    <h2 class="section-title">
        Pending Trade from @<?php echo $pendingTrade['Fname'].' '.$pendingTrade['Lname']; ?>
    </h2>
    <div class="trade-content">
        <div class="rock-section">
            <h3>Their Rock</h3>
            <div class="rock-card">
                <img src="assets/images/<?php echo $pendingTrade['requesterRockImg']; ?>" alt="<?php echo $pendingTrade['requesterRockName']; ?>">
                <p class="rock-name"><?php echo $pendingTrade['requesterRockName']; ?></p>
                <p class="rock-price">💎 <?php echo $pendingTrade['requesterRockPrice']; ?></p>
            </div>
        </div>
        <div class="trade-arrow">→</div>
        <div class="rock-section">
            <h3>Your Rock</h3>
            <div class="rock-card">
                <img src="assets/images/<?php echo $pendingTrade['targetRockImg']; ?>" alt="<?php echo $pendingTrade['targetRockName']; ?>">
                <p class="rock-name"><?php echo $pendingTrade['targetRockName']; ?></p>
                <p class="rock-price">💎 <?php echo $pendingTrade['targetRockPrice']; ?></p>
            </div>
        </div>
    </div>
    <div class="trade-actions">
        <button class="trade-btn accept-btn" onclick="acceptTrade(<?php echo $pendingTrade['TradeID']; ?>)">Accept</button>
        <button class="trade-btn reject-btn" onclick="rejectTrade(<?php echo $pendingTrade['TradeID']; ?>)">Reject</button>
    </div>
<?php else: ?>
    <h2 class="section-title">Trade Proposal</h2>
    <p class="trade-subtitle">
        Trading With:
        <?php if ($targetUserID): ?>
            <strong>@<?php echo htmlspecialchars($targetUserName); ?></strong>
        <?php else: ?>
            <form method="GET">
                <input type="text" name="targetCustID" placeholder="User ID" required>
                <input type="text" name="targetUserName" placeholder="User Name" required>
                <button type="submit">Select User</button>
            </form>
        <?php endif; ?>
    </p>

    <div class="trade-content">
        <!-- Your Rock -->
        <div class="rock-section">
            <h3>Your Rock</h3>
            <select id="yourRockSelect">
                <option value="">-- Select Your Rock --</option>
                <?php foreach ($yourRocks as $rock): ?>
                    <option value="<?php echo $rock['RockID']; ?>" data-name="<?php echo $rock['Name']; ?>" data-price="<?php echo $rock['Price']; ?>" data-image="<?php echo $rock['Image']; ?>"><?php echo $rock['Name']; ?> (💎 <?php echo $rock['Price']; ?>)</option>
                <?php endforeach; ?>
            </select>
            <div id="yourRockDisplay" class="rock-card">
                <img src="" alt="No Rock Selected" style="display:none;">
                <p class="rock-name">Selected: None</p>
                <p class="rock-price"></p>
            </div>
        </div>

        <div class="trade-arrow">→</div>

        <!-- Target Rock -->
        <div class="rock-section">
            <h3><?php echo htmlspecialchars($targetUserName ?? 'Target User'); ?>'s Rock</h3>
            <select id="targetRockSelect">
                <option value="">-- Select Target Rock --</option>
                <?php foreach ($targetRocks as $rock): ?>
                    <option value="<?php echo $rock['RockID']; ?>" data-name="<?php echo $rock['Name']; ?>" data-price="<?php echo $rock['Price']; ?>" data-image="<?php echo $rock['Image']; ?>"><?php echo $rock['Name']; ?> (💎 <?php echo $rock['Price']; ?>)</option>
                <?php endforeach; ?>
            </select>
            <div id="targetRockDisplay" class="rock-card">
                <img src="" alt="No Rock Selected" style="display:none;">
                <p class="rock-name">Selected: None</p>
                <p class="rock-price"></p>
            </div>
        </div>
    </div>

    <div class="trade-actions">
        <button class="trade-btn accept-btn" onclick="requestTrade()">Initiate Trade</button>
    </div>
<?php endif; ?>

</div>
</main>

<footer>&copy; 2025 The Pet Rock Exchange. All rights reserved.</footer>

<script>
const yourSelect = document.getElementById('yourRockSelect');
const targetSelect = document.getElementById('targetRockSelect');
const yourDisplay = document.getElementById('yourRockDisplay');
const targetDisplay = document.getElementById('targetRockDisplay');

function updateDisplay(select, display) {
    const option = select.selectedOptions[0];
    const img = display.querySelector('img');
    const name = display.querySelector('.rock-name');
    const price = display.querySelector('.rock-price');

    if(option.value){
        img.src = `assets/images/${option.dataset.image}`;
        img.style.display = 'block';
        name.textContent = `Selected: ${option.dataset.name}`;
        price.textContent = `💎 ${option.dataset.price}`;
    } else {
        img.style.display = 'none';
        name.textContent = 'Selected: None';
        price.textContent = '';
    }
}

yourSelect.addEventListener('change', ()=>updateDisplay(yourSelect, yourDisplay));
targetSelect.addEventListener('change', ()=>updateDisplay(targetSelect, targetDisplay));

function requestTrade() {
    const yourRockID = yourSelect.value;
    const targetRockID = targetSelect.value;
    const targetCustID = '<?php echo $targetUserID; ?>';

    if(!yourRockID || !targetRockID) return alert("Select both rocks!");

    fetch('php/initiate_trade.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`requesterRockID=${yourRockID}&targetRockID=${targetRockID}&targetCustID=${targetCustID}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('✅ Trade request sent!');

            // Reset dropdowns
            yourSelect.selectedIndex = 0;
            targetSelect.selectedIndex = 0;

            // Reset preview cards
            updateDisplay(yourSelect, yourDisplay);
            updateDisplay(targetSelect, targetDisplay);

        } else {
            alert('❌ ' + (data.error || 'Trade failed.'));
        }
    });
}


function acceptTrade(tradeID){
    fetch('php/respond_trade.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`tradeID=${tradeID}&action=accept`
    }).then(res=>res.json()).then(data=>{
        if(data.success) alert('✅ '+data.message);
        else alert('❌ '+(data.error||'Failed.'));
        location.reload();
    });
}

function rejectTrade(tradeID){
    fetch('php/respond_trade.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`tradeID=${tradeID}&action=reject`
    }).then(res=>res.json()).then(data=>{
        if(data.success) alert('❌ '+data.message);
        else alert('❌ '+(data.error||'Failed.'));
        location.reload();
    });
}
</script>
</body>
</html>
