<?php
header("Content-Type: application/json");
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$fname = trim($data['fname'] ?? '');
$lname = trim($data['lname'] ?? '');
$dob = $data['dob'] ?? '';
$address = trim($data['address'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

$response = ['success' => false, 'message' => 'Registration failed'];

if ($fname && $lname && $dob && $address && $email && $password) {
    // Check if email exists
    $stmt = $conn->prepare("SELECT CustID FROM customers WHERE Email=? AND Deleted='N'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['message'] = "Email already registered";
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO customers (FName, LName, DOB, Email, Address, Password, Deleted) VALUES (?, ?, ?, ?, ?, ?, 'N')");
        $stmt->bind_param("ssssss", $fname, $lname, $dob, $email, $address, $password);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Registration successful";
        } else {
            $response['message'] = "Database insert failed";
        }
    }
    $stmt->close();
}
$conn->close();
echo json_encode($response);
exit;
