<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header('Location: ../room_types.php');
    exit;
}

// Validate input
if (!isset($_POST['room_type_id']) || !isset($_POST['hotel_id'])) {
    $_SESSION['error'] = "Missing required parameters";
    header('Location: ../room_types.php');
    exit;
}

$room_type_id = intval($_POST['room_type_id']);
$hotel_id = intval($_POST['hotel_id']);

try {
    // Start transaction
    $conn->beginTransaction();

    // First check if room type exists and belongs to the hotel
    $check_sql = "SELECT * FROM room_types WHERE room_type_id = ? AND hotel_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$room_type_id, $hotel_id]);
    $room_type = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room_type) {
        throw new Exception("Room type not found or doesn't belong to this hotel");
    }

    // Check if there are any existing bookings for this room type
    $booking_check_sql = "SELECT COUNT(*) FROM room_bookings rb 
                         INNER JOIN rooms r ON rb.room_id = r.room_id 
                         WHERE r.room_type_id = ?";
    $booking_check_stmt = $conn->prepare($booking_check_sql);
    $booking_check_stmt->execute([$room_type_id]);
    $booking_count = $booking_check_stmt->fetchColumn();

    if ($booking_count > 0) {
        throw new Exception("Cannot delete room type: There are existing bookings for this room type");
    }

    // Delete associated rooms
    $delete_rooms_sql = "DELETE FROM rooms WHERE room_type_id = ?";
    $delete_rooms_stmt = $conn->prepare($delete_rooms_sql);
    $delete_rooms_stmt->execute([$room_type_id]);

    // Get images to delete
    $images = json_decode($room_type['images'] ?? '[]', true);

    // Delete room type from database
    $delete_sql = "DELETE FROM room_types WHERE room_type_id = ? AND hotel_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute([$room_type_id, $hotel_id]);

    // If deletion was successful, delete the image files
    if ($delete_stmt->rowCount() > 0) {
        // Delete image files
        foreach ($images as $image) {
            $image_path = '../../' . $image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Try to remove the room type's image directory
        $room_type_dir = "../../uploads/img/rooms/{$room_type_id}";
        if (is_dir($room_type_dir)) {
            // Try to remove any remaining files in the directory
            $files = glob($room_type_dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            // Try to remove the directory itself
            rmdir($room_type_dir);
        }
    }

    // Commit transaction
    $conn->commit();

    $_SESSION['success'] = "Room type deleted successfully";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

// Redirect back to room types page
header("Location: ../room_types.php?hotel_id=" . $hotel_id);
exit;
