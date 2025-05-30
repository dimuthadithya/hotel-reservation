<?php
require_once '../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, profile_image)
            VALUES (:first_name, :last_name, :email, :password, :phone)");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
