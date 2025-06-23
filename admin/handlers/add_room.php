<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../hotels.php');
    exit;
}

if (!isset($_POST['hotel_id']) || !isset($_POST['room_type_id']) || !isset($_POST['room_number'])) {
    $_SESSION['error'] = 'All required fields must be filled.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

try {
    $hotel_id = intval($_POST['hotel_id']);
    $room_type_id = intval($_POST['room_type_id']);
    $room_number = trim($_POST['room_number']);
    $floor_number = intval($_POST['floor_number']);
    $status = $_POST['status'];

    // Check if room number already exists in this hotel
    $check_sql = "SELECT room_id FROM rooms WHERE hotel_id = ? AND room_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$hotel_id, $room_number]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = 'Room number already exists in this hotel.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Insert new room
    $sql = "INSERT INTO rooms (hotel_id, room_type_id, room_number, floor_number, status) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$hotel_id, $room_type_id, $room_number, $floor_number, $status]);

    $_SESSION['success'] = 'Room added successfully.';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error adding room. Please try again.';
}

header('Location: ../manage_hotel_rooms.php?hotel_id=' . $hotel_id);
