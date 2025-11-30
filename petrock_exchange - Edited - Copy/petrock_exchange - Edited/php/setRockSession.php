<?php
session_start();
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['rockID'])) {
    $_SESSION['RockID'] = intval($data['rockID']);
}

echo json_encode(['success' => true]);
