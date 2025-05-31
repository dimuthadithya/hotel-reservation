<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get form data
    $hotel_id = $_POST['hotel_id'];
    $hotel_name = $_POST['hotel_name'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $star_rating = $_POST['star_rating'];
    $property_type = $_POST['property_type'];
    $contact_phone = $_POST['contact_phone'];
    $contact_email = $_POST['contact_email'];
    $website_url = $_POST['website_url'];
    $total_rooms = $_POST['total_rooms'];
    $status = $_POST['status'];

    // Update hotel details
    $sql = "UPDATE hotels SET 
            hotel_name = :hotel_name,
            description = :description,
            address = :address,
            district = :district,
            province = :province,
            star_rating = :star_rating,
            property_type = :property_type,
            contact_phone = :contact_phone,
            contact_email = :contact_email,
            website_url = :website_url,
            total_rooms = :total_rooms,
            status = :status,
            updated_at = CURRENT_TIMESTAMP
            WHERE hotel_id = :hotel_id";

    $stmt = $conn->prepare($sql);

    $params = [
        'hotel_id' => $hotel_id,
        'hotel_name' => $hotel_name,
        'description' => $description,
        'address' => $address,
        'district' => $district,
        'province' => $province,
        'star_rating' => $star_rating,
        'property_type' => $property_type,
        'contact_phone' => $contact_phone,
        'contact_email' => $contact_email,
        'website_url' => $website_url,
        'total_rooms' => $total_rooms,
        'status' => $status
    ];

    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes made to the hotel']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
