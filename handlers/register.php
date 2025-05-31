<?php
require_once '../config/db.php';


if (isset($_POST['register']) && $_POST['first_name'] && $_POST['last_name'] && $_POST['email'] && $_POST['password']) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone)
            VALUES (:first_name, :last_name, :email, :password, :phone)");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header('Location: ../login.php');
        exit;
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
