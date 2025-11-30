<?php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db = "petrock_exchange";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["message" => "Database connection failed: " . $conn->connect_error]));
}
?>