<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../hotels.php');
    exit;
}

if (!isset($_POST['hotel_id']) || empty($_POST['hotel_id'])) {
    header('Location: ../hotels.php');
    exit;
}

try {
    $hotel_id = intval($_POST['hotel_id']);

    // Delete the hotel
    $delete_sql = "DELETE FROM hotels WHERE hotel_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute([$hotel_id]);

    header('Location: ../hotels.php');
} catch (PDOException $e) {
    header('Location: ../hotels.php');
}
