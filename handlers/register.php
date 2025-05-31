<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

// Initialize errors array
$errors = [];

// Validate and sanitize inputs
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirmPassword'] ?? '';
$phone = trim($_POST['phone'] ?? '');

// Validate first name
if (empty($first_name)) {
    $errors['first_name'] = 'First name is required';
} elseif (strlen($first_name) > 50) {
    $errors['first_name'] = 'First name cannot exceed 50 characters';
} elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $first_name)) {
    $errors['first_name'] = 'First name can only contain letters, spaces, hyphens and apostrophes';
}

// Validate last name
if (empty($last_name)) {
    $errors['last_name'] = 'Last name is required';
} elseif (strlen($last_name) > 50) {
    $errors['last_name'] = 'Last name cannot exceed 50 characters';
} elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $last_name)) {
    $errors['last_name'] = 'Last name can only contain letters, spaces, hyphens and apostrophes';
}

// Validate email
if (empty($email)) {
    $errors['email'] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
} elseif (strlen($email) > 100) {
    $errors['email'] = 'Email cannot exceed 100 characters';
}

// Validate password
if (empty($password)) {
    $errors['password'] = 'Password is required';
} elseif (strlen($password) < 8) {
    $errors['password'] = 'Password must be at least 8 characters long';
} elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
    $errors['password'] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
}

// Validate password confirmation
if ($password !== $confirm_password) {
    $errors['confirm_password'] = 'Passwords do not match';
}

// Validate phone (optional)
if (!empty($phone) && !preg_match("/^[0-9\s\-\(\)\+]{10,20}$/", $phone)) {
    $errors['phone'] = 'Invalid phone number format';
}

// If there are validation errors, redirect back with error messages
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_form_data'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone
    ];
    header('Location: ../register.php');
    exit;
}

try {
    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_stmt->execute([$email]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['register_errors'] = ['email' => 'This email is already registered'];
        $_SESSION['register_form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $phone
        ];
        header('Location: ../register.php');
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (first_name, last_name, email, password, phone, role, account_status)
        VALUES (:first_name, :last_name, :email, :password, :phone, 'user', 'active')
    ");

    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':password' => $password_hash,
        ':phone' => $phone
    ]);

    // Set success message and redirect to login
    $_SESSION['success'] = 'Registration successful! Please login to continue.';
    header('Location: ../login.php');
    exit;
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    $_SESSION['register_errors'] = ['system' => 'An error occurred during registration. Please try again later.'];
    header('Location: ../register.php');
    exit;
}
