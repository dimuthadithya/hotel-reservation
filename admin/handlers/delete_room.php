<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../hotels.php');
    exit;
}

if (!isset($_POST['room_id']) || !isset($_POST['hotel_id'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

try {
    $room_id = intval($_POST['room_id']);
    $hotel_id = intval($_POST['hotel_id']);

    // Delete room
    $sql = "DELETE FROM rooms WHERE room_id = ? AND hotel_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$room_id, $hotel_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = 'Room deleted successfully.';
    } else {
        $_SESSION['error'] = 'Room not found.';
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error deleting room. Please try again.';
}

header('Location: ../manage_hotel_rooms.php?hotel_id=' . $hotel_id);
