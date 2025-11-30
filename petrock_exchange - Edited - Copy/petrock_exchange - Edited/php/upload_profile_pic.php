<?php
session_start();
require "db.php";

if (!isset($_SESSION['CustID'])) {
    header("Location: ../account.php");
    exit;
}

$CustID = $_SESSION['CustID'];

if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['profilePic']['tmp_name'];
    $fileName = basename($_FILES['profilePic']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];

    if (!in_array($fileExt, $allowed)) {
        die("Invalid file type. Only JPG, PNG, GIF allowed.");
    }

    // Create unique filename to avoid collisions
    $newFileName = "profile_{$CustID}_" . time() . "." . $fileExt;
    $uploadDir = "../assets/images/";
    $destination = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmp, $destination)) {
        // Update DB
        $stmt = $conn->prepare("UPDATE customers SET ProfilePic=? WHERE CustID=?");
        $stmt->bind_param("si", $newFileName, $CustID);
        $stmt->execute();

        header("Location: ../account.php");
        exit;
    } else {
        die("Failed to upload file.");
    }
} else {
    die("No file uploaded or error occurred.");
}
