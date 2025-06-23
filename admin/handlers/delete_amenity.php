<?php
require_once '../../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $amenityId = isset($_POST['amenityId']) ? (int)$_POST['amenityId'] : 0;

    if (!$amenityId) {
        throw new Exception('Invalid amenity ID');
    }

    // First check if the amenity exists
    $stmt = $conn->prepare("SELECT amenity_id FROM amenities WHERE amenity_id = ?");
    $stmt->execute([$amenityId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Amenity not found');
    }

    // Check if the amenity is being used by any hotels
    $stmt = $conn->prepare("SELECT COUNT(*) FROM hotel_amenities WHERE amenity_id = ?");
    $stmt->execute([$amenityId]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        throw new Exception('Cannot delete this amenity as it is being used by one or more hotels');
    }

    // Delete the amenity
    $stmt = $conn->prepare("DELETE FROM amenities WHERE amenity_id = ?");
    $stmt->execute([$amenityId]);

    // Redirect back with success message
    header('Location: ../amenities.php?success=Amenity deleted successfully');
    exit;
} catch (Exception $e) {
    // Redirect back with error message
    header('Location: ../amenities.php?error=' . urlencode($e->getMessage()));
    exit;
}
