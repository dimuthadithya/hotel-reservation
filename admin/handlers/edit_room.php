<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request to fetch room details
    if (!isset($_GET['room_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Room ID is required']);
        exit;
    }

    $room_id = intval($_GET['room_id']);

    try {
        // Get room details including hotel and type information
        $sql = "SELECT r.*, h.hotel_name, h.hotel_id, rt.type_name, rt.room_type_id 
                FROM rooms r 
                JOIN hotels h ON r.hotel_id = h.hotel_id 
                JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                WHERE r.room_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            echo json_encode(['status' => 'error', 'message' => 'Room not found']);
            exit;
        }

        // Get room types for the hotel
        $types_sql = "SELECT room_type_id, type_name FROM room_types WHERE hotel_id = ? ORDER BY type_name";
        $types_stmt = $conn->prepare($types_sql);
        $types_stmt->execute([$room['hotel_id']]);
        $room_types = $types_stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            'status' => 'success',
            'data' => [
                'room' => $room,
                'room_types' => $room_types
            ]
        ];

        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['room_id'])) {
        $_SESSION['error'] = "Room ID is required";
        header('Location: ../rooms.php');
        exit;
    }

    try {
        $room_id = intval($_POST['room_id']);
        $room_type_id = intval($_POST['room_type_id']);
        $room_number = trim($_POST['room_number']);
        $floor_number = intval($_POST['floor_number']);
        $status = $_POST['status'];

        // Start transaction
        $conn->beginTransaction();

        // Get current room details
        $current_sql = "SELECT r.*, rt.hotel_id FROM rooms r 
                       JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                       WHERE r.room_id = ?";
        $current_stmt = $conn->prepare($current_sql);
        $current_stmt->execute([$room_id]);
        $current_room = $current_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current_room) {
            throw new Exception("Room not found");
        }

        // Validate room number format
        if (empty($room_number) || strlen($room_number) > 10) {
            throw new Exception("Invalid room number format");
        }        // Check if room number already exists in this hotel (excluding current room)
        $check_sql = "SELECT COUNT(*) FROM rooms r 
                     JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                     WHERE rt.hotel_id = ? AND r.room_number = ? AND r.room_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$current_room['hotel_id'], $room_number, $room_id]);
        if ($check_stmt->fetchColumn() > 0) {
            throw new Exception("Room number already exists in this hotel");
        }

        // Validate that the new room type belongs to the same hotel
        $type_check_sql = "SELECT hotel_id FROM room_types WHERE room_type_id = ?";
        $type_check_stmt = $conn->prepare($type_check_sql);
        $type_check_stmt->execute([$room_type_id]);
        $type_hotel = $type_check_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$type_hotel || $type_hotel['hotel_id'] !== $current_room['hotel_id']) {
            throw new Exception("Invalid room type selected");
        }

        // Check if room can be updated
        if ($current_room['status'] === 'occupied' && $status !== 'occupied') {
            // Check if room has active bookings
            $booking_sql = "SELECT COUNT(*) FROM room_bookings 
                          WHERE room_id = ? AND status = 'confirmed'";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->execute([$room_id]);
            if ($booking_stmt->fetchColumn() > 0) {
                throw new Exception("Cannot change status: Room has active bookings");
            }
        }

        // Update room details
        $sql = "UPDATE rooms SET 
                room_type_id = ?, 
                room_number = ?, 
                floor_number = ?, 
                status = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE room_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $room_type_id,
            $room_number,
            $floor_number,
            $status,
            $room_id
        ]);        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Room updated successfully";
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirect back
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: ../rooms.php');
    }
    exit;
}
