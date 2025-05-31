<?php
require_once '../../config/db.php';

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get form data
    $amenityId = isset($_POST['amenityId']) ? (int)$_POST['amenityId'] : 0;
    $amenityName = trim($_POST['amenityName'] ?? '');
    $iconClass = trim($_POST['iconClass'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validate required fields
    if (!$amenityId || empty($amenityName) || empty($iconClass) || empty($category)) {
        throw new Exception('Required fields cannot be empty');
    }

    // Validate category
    $validCategories = ['basic', 'comfort', 'business', 'recreation', 'accessibility'];
    if (!in_array($category, $validCategories)) {
        throw new Exception('Invalid category');
    }

    // Check if amenity exists
    $stmt = $conn->prepare("SELECT amenity_id FROM amenities WHERE amenity_id = ?");
    $stmt->execute([$amenityId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Amenity not found');
    }

    // Check if the new name already exists for another amenity
    $stmt = $conn->prepare("SELECT amenity_id FROM amenities WHERE amenity_name = ? AND amenity_id != ?");
    $stmt->execute([$amenityName, $amenityId]);

    if ($stmt->rowCount() > 0) {
        throw new Exception('An amenity with this name already exists');
    }

    // Update the amenity
    $stmt = $conn->prepare("
        UPDATE amenities 
        SET amenity_name = ?, 
            icon_class = ?, 
            category = ?, 
            description = ?
        WHERE amenity_id = ?
    ");

    $stmt->execute([
        $amenityName,
        $iconClass,
        $category,
        $description,
        $amenityId
    ]);

    // Redirect back with success message
    header('Location: ../amenities.php?success=Amenity updated successfully');
    exit;
} catch (Exception $e) {
    // Redirect back with error message
    header('Location: ../amenities.php?error=' . urlencode($e->getMessage()));
    exit;
}
