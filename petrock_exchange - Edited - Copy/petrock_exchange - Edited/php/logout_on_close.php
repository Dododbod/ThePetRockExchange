<?php
// Start the session
session_start();

// Optionally, force a short session lifetime (e.g., until browser closes)
ini_set('session.cookie_lifetime', 0); // 0 means until browser closes
ini_set('session.gc_maxlifetime', 1440); // Session data lifetime on server in seconds

// If the session exists, destroy it
if (isset($_SESSION['CustID'])) {
    // Unset all session variables
    $_SESSION = [];

    // Destroy session cookie on client
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();
}

// Redirect to login page or homepage
header("Location: index.php");
exit;
