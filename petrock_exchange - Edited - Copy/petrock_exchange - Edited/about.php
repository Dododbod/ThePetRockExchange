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
<title>About — The Pet Rock Exchange</title>
<link rel="stylesheet" href="about.css">
<link rel="icon" href="assets/icons/teamonelogo.png">
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
            <li><a href="about.php" class="active">About</a></li>
        </ul>
    </div>
</nav>

<main>
    <div class="container">
        <h2 class="section-title">Meet Our Team</h2>
        <div class="team-grid">
            <!-- Team Member 1 -->
            <div class="team-card">
                <img src="Image/Celine.png" alt="Celine Nicole Parra">
                <div class="team-info">
                    <h3>Celine Nicole Parra</h3>
                    <p class="muted">PNW 2026</p>
                    <p>Data Analysis | Healthcare Informatics | Python</p>
                    <a href="mailto:celine26.cp@gmail.com"><button>Email</button></a>
                </div>
            </div>

            <!-- Team Member 2 -->
            <div class="team-card">
                <img src="Image/John.png" alt="John Doe">
                <div class="team-info">
                    <h3>John Doe</h3>
                    <p class="muted">PNW 2026</p>
                    <p>Backend Development | SQL | Node.js</p>
                    <a href="mailto:johndoe@example.com"><button>Email</button></a>
                </div>
            </div>

            <!-- Team Member 3 -->
            <div class="team-card">
                <img src="Image/Jane.png" alt="Jane Smith">
                <div class="team-info">
                    <h3>Jane Smith</h3>
                    <p class="muted">PNW 2026</p>
                    <p>UI/UX Design | Frontend | CSS</p>
                    <a href="mailto:janesmith@example.com"><button>Email</button></a>
                </div>
            </div>

            <!-- Team Member 4 -->
            <div class="team-card">
                <img src="Image/Mike.png" alt="Mike Johnson">
                <div class="team-info">
                    <h3>Mike Johnson</h3>
                    <p class="muted">PNW 2026</p>
                    <p>Database Design | Security | Python</p>
                    <a href="mailto:mikejohnson@example.com"><button>Email</button></a>
                </div>
            </div>
        </div>

        <h2 class="section-title">Project Links</h2>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-info">
                    <h3>GitHub</h3>
                    <p>Repositories, issues, and project boards</p>
                    <a href="https://github.com/Dododbod/CS442-Project0/tree/main" target="_blank"><button>Visit</button></a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>
    &copy; 2025 The Pet Rock Exchange. All rights reserved.
</footer>

</body>
</html>
