<?php
header("Content-Type: application/json");
session_start();

if (isset($_SESSION['CustID'])) {
    echo json_encode(['loggedIn' => true, 'CustID' => $_SESSION['CustID']]);
} else {
    echo json_encode(['loggedIn' => false]);
}
