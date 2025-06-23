<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    $hotel_id = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : 0;
    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

    if (!$hotel_id) {
        throw new Exception('Invalid hotel ID');
    }

    // Start transaction
    $conn->beginTransaction();

    // Remove existing amenities for this hotel
    $delete_sql = "DELETE FROM hotel_amenities WHERE hotel_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute([$hotel_id]);

    // Add new amenities
    if (!empty($amenities)) {
        $insert_sql = "INSERT INTO hotel_amenities (hotel_id, amenity_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);

        foreach ($amenities as $amenity_id) {
            $insert_stmt->execute([$hotel_id, $amenity_id]);
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Hotel amenities updated successfully']);
} catch (Exception $e) {
    // Rollback on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
