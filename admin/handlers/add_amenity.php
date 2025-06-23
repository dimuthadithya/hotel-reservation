<?php
require_once '../../config/db.php';

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get form data
    $amenityName = trim($_POST['amenityName']);
    $iconClass = trim($_POST['iconClass']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    // Validate data
    if (empty($amenityName) || empty($iconClass) || empty($category)) {
        throw new Exception('Required fields cannot be empty');
    }

    // Validate category
    $validCategories = ['basic', 'comfort', 'business', 'recreation', 'accessibility'];
    if (!in_array($category, $validCategories)) {
        throw new Exception('Invalid category');
    }

    // Check if amenity already exists
    $stmt = $conn->prepare("SELECT amenity_id FROM amenities WHERE amenity_name = ?");
    $stmt->execute([$amenityName]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('Amenity already exists');
    }

    // Insert new amenity
    $stmt = $conn->prepare("
        INSERT INTO amenities (amenity_name, icon_class, category, description)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $amenityName,
        $iconClass,
        $category,
        $description
    ]);

    // Redirect back with success message
    header('Location: ../amenities.php?success=Amenity added successfully');
    exit;
} catch (Exception $e) {
    // Redirect back with error message
    header('Location: ../amenities.php?error=' . urlencode($e->getMessage()));
    exit;
}
