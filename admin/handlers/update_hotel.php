<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    $conn->beginTransaction();

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

    $params = [
        ':hotel_id' => $hotel_id,
        ':hotel_name' => $hotel_name,
        ':description' => $description,
        ':address' => $address,
        ':district' => $district,
        ':province' => $province,
        ':star_rating' => $star_rating,
        ':property_type' => $property_type,
        ':contact_phone' => $contact_phone,
        ':contact_email' => $contact_email,
        ':website_url' => $website_url,
        ':total_rooms' => $total_rooms,
        ':status' => $status
    ];

    // Handle image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['main_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            throw new Exception('File size too large. Maximum size is 2MB.');
        }

        $fileName = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadDir = "../../uploads/img/hotels/{$hotel_id}";

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], "{$uploadDir}/{$fileName}")) {
            // Delete old image if exists
            $stmt = $conn->prepare("SELECT main_image FROM hotels WHERE hotel_id = ?");
            $stmt->execute([$hotel_id]);
            $oldImage = $stmt->fetchColumn();

            if ($oldImage && file_exists("{$uploadDir}/{$oldImage}")) {
                unlink("{$uploadDir}/{$oldImage}");
            }

            $params[':main_image'] = $fileName;
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
                    main_image = :main_image,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE hotel_id = :hotel_id";
        } else {
            throw new Exception('Failed to upload image.');
        }
    } else {
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
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Hotel updated successfully']);
} catch (Exception $e) {
    if ($conn) {
        $conn->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
