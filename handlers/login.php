<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

// Initialize error array
$errors = [];

// Validate email and password are set
if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
    $errors[] = "Email is required";
}
if (!isset($_POST['password']) || empty(trim($_POST['password']))) {
    $errors[] = "Password is required";
}

// If there are any missing field errors, redirect back
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header('Location: ../login.php');
    exit;
}

// Clean the inputs
$email = trim($_POST['email']);
$password = $_POST['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_errors'] = ['Invalid email format'];
    header('Location: ../login.php');
    exit;
}

try {
    // Check if account exists and get user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['login_errors'] = ['Invalid email or password'];
        header('Location: ../login.php');
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Increment login attempts
        $updateAttempts = $conn->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE user_id = ?");
        $updateAttempts->execute([$user['user_id']]);

        $_SESSION['login_errors'] = ['Invalid email or password'];
        header('Location: ../login.php');
        exit;
    }

    // Check account status
    if ($user['account_status'] !== 'active') {
        $_SESSION['login_errors'] = ['Your account is ' . $user['account_status'] . '. Please contact support.'];
        header('Location: ../login.php');
        exit;
    }

    // Check if account is temporarily locked
    if ($user['locked_until'] !== null && new DateTime($user['locked_until']) > new DateTime()) {
        $_SESSION['login_errors'] = ['Account is temporarily locked. Please try again later.'];
        header('Location: ../login.php');
        exit;
    }

    // Reset login attempts and clear lock if login is successful
    $resetAttempts = $conn->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL, last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
    $resetAttempts->execute([$user['user_id']]);

    // Set session data
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['role'] = $user['role'];

    // Redirect based on role
    if ($user['role'] === 'admin') {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
} catch (PDOException $e) {
    $_SESSION['login_errors'] = ['An error occurred. Please try again later.'];
    header('Location: ../login.php');
    exit;
}
