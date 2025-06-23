<?php

require_once '../config/db.php';
session_start();

if (isset($_POST['update_profile'])) {


    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        exit;
    }

    $user_id = $_SESSION['user_id'];
    // Get the form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone WHERE user_id = :user_id");
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully.";
        // Optionally, you can redirect to a profile page or display updated information
        header('Location: ../dashboard.php');
        exit;
    } else {
        echo "Error updating profile: " . implode(", ", $stmt->errorInfo());
    }
} else {
    echo "Invalid request method.";
}
