<?php
header("Content-Type: application/json");
require "db.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

$response = ['success' => false, 'message' => 'Invalid email or password'];

if ($email && $password) {
    $stmt = $conn->prepare("SELECT CustID, Password FROM customers WHERE Email=? AND Deleted='N'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($custID, $dbPassword);

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        if ($password === $dbPassword) { // plain text match
            $_SESSION['CustID'] = $custID;
            $response['success'] = true;
            $response['message'] = "Login successful";
        } else {
            $response['message'] = "Incorrect password";
        }
    } else {
        $response['message'] = "Email not found";
    }

    $stmt->close();
}
$conn->close();
echo json_encode($response);
exit;
