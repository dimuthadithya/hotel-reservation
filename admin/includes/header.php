<?php
include_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Pearl Stay</title>
    <!-- Favicon -->
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="../assets/favicon_io/apple-touch-icon.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="../assets/favicon_io/favicon-32x32.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="../assets/favicon_io/favicon-16x16.png" />
    <link rel="manifest" href="../assets/favicon_io/site.webmanifest" />
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet" /> <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet" />
    <link href="../assets/css/nav.css" rel="stylesheet" />
    <link href="css/admin.css" rel="stylesheet" />
</head>

<body>
    <div class="admin-container">