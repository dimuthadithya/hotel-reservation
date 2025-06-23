<?php

require_once '../config/db.php';
session_start();

if (isset($_POST['update_password'])) {


    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        exit;
    }

    $user_id = $_SESSION['user_id'];
    // Get the form data
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo "User not found.";
        exit;
    }

    // Verify the current password
    if (!password_verify($current_password, $user['password'])) {
        echo "Current password is incorrect.";
        exit;
    }
    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        echo "New password and confirm password do not match.";
        exit;
    }

    // Update the password in the database  
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        header('Location: ../dashboard.php');
        exit;
    } else {
        echo "Failed to update password.";
    }
} else {
    echo "Invalid request method.";
}
