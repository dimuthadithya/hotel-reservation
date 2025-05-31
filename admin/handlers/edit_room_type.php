<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header('Location: ../room_types.php');
    exit;
}

try {
    // Get form data
    $room_type_id = $_POST['room_type_id'];
    $hotel_id = $_POST['hotel_id'];
    $type_name = $_POST['type_name'];
    $description = $_POST['description'];
    $max_occupancy = $_POST['max_occupancy'];
    $base_price = $_POST['base_price'];
    $room_size = $_POST['room_size'];
    $bed_type = $_POST['bed_type'];
    $total_rooms = $_POST['total_rooms'];
    $status = $_POST['status'];

    // Handle amenities
    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];
    $amenities_json = json_encode($amenities);

    // Update room type details
    $sql = "UPDATE room_types SET 
            type_name = :type_name,
            description = :description,
            max_occupancy = :max_occupancy,
            base_price = :base_price,
            room_size = :room_size,
            bed_type = :bed_type,
            total_rooms = :total_rooms,
            room_amenities = :room_amenities,
            status = :status
            WHERE room_type_id = :room_type_id 
            AND hotel_id = :hotel_id";

    $stmt = $conn->prepare($sql);
    $params = [
        'room_type_id' => $room_type_id,
        'hotel_id' => $hotel_id,
        'type_name' => $type_name,
        'description' => $description,
        'max_occupancy' => $max_occupancy,
        'base_price' => $base_price,
        'room_size' => $room_size,
        'bed_type' => $bed_type,
        'total_rooms' => $total_rooms,
        'room_amenities' => $amenities_json,
        'status' => $status
    ];

    $stmt->execute($params);    // Handle new images if any are uploaded
    if (!empty($_FILES['room_images']['name'][0])) {
        $uploadDir = "../../uploads/img/rooms/{$room_type_id}/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get existing images
        $stmt = $conn->prepare("SELECT images FROM room_types WHERE room_type_id = ?");
        $stmt->execute([$room_type_id]);
        $row = $stmt->fetch();
        $existingImages = json_decode($row['images'] ?? '[]', true);

        // Check total number of images
        $totalImages = count($existingImages);
        $maxAllowed = 5;

        $newImages = [];
        foreach ($_FILES['room_images']['tmp_name'] as $key => $tmp_name) {
            if ($totalImages >= $maxAllowed) {
                $_SESSION['error'] = "Maximum 5 images allowed per room type.";
                header("Location: ../room_types.php?hotel_id=" . $hotel_id);
                exit;
            }

            if ($_FILES['room_images']['error'][$key] === UPLOAD_ERR_OK) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = mime_content_type($tmp_name);
                if (!in_array($fileType, $allowedTypes)) {
                    continue;
                }

                // Generate unique filename
                $filename = uniqid() . '_' . $_FILES['room_images']['name'][$key];
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($tmp_name, $filepath)) {
                    $newImages[] = 'uploads/img/rooms/' . $room_type_id . '/' . $filename;
                    $totalImages++;
                }
            }
        }

        // Combine existing and new images
        $allImages = array_merge($existingImages, $newImages);

        // Update the images in database
        $stmt = $conn->prepare("UPDATE room_types SET images = ? WHERE room_type_id = ?");
        $stmt->execute([json_encode($allImages), $room_type_id]);
    }

    $_SESSION['success'] = "Room type updated successfully";
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: ../room_types.php?hotel_id=" . $hotel_id);
exit;
