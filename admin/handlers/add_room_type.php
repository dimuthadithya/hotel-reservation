<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../hotels.php');
    exit;
}

// Validate required fields
$required_fields = ['hotel_id', 'type_name', 'description', 'max_occupancy', 'base_price', 'room_size', 'bed_type', 'total_rooms', 'status'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $_SESSION['error'] = "All required fields must be filled.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

try {
    // Get form data
    $hotel_id = intval($_POST['hotel_id']);
    $type_name = trim($_POST['type_name']);
    $description = trim($_POST['description']);
    $max_occupancy = intval($_POST['max_occupancy']);
    $base_price = floatval($_POST['base_price']);
    $room_size = trim($_POST['room_size']);
    $bed_type = trim($_POST['bed_type']);
    $total_rooms = intval($_POST['total_rooms']);
    $status = $_POST['status'];

    // Process amenities
    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];
    $amenities_json = json_encode($amenities);

    // Check if room type name already exists for this hotel
    $check_sql = "SELECT room_type_id FROM room_types WHERE hotel_id = ? AND type_name = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$hotel_id, $type_name]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = 'Room type name already exists in this hotel.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Process images
    $images = [];
    if (isset($_FILES['room_images']) && !empty($_FILES['room_images']['name'][0])) {
        $upload_path = "../../uploads/img/room_types/" . $hotel_id . "/";

        // Create upload directory if it doesn't exist
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Maximum 5 images
        $max_images = 5;
        $file_count = min(count($_FILES['room_images']['name']), $max_images);

        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['room_images']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['room_images']['tmp_name'][$i];
                $name = $_FILES['room_images']['name'][$i];
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                // Validate file type
                $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($extension, $allowed_types)) {
                    continue;
                }

                // Generate unique filename
                $new_filename = uniqid() . '.' . $extension;
                $destination = $upload_path . $new_filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $images[] = 'uploads/img/room_types/' . $hotel_id . '/' . $new_filename;
                }
            }
        }
    }

    // Insert room type
    $sql = "INSERT INTO room_types (
                hotel_id, type_name, description, max_occupancy, base_price, 
                room_size, bed_type, total_rooms, room_amenities, images, status
            ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $hotel_id,
        $type_name,
        $description,
        $max_occupancy,
        $base_price,
        $room_size,
        $bed_type,
        $total_rooms,
        $amenities_json,
        json_encode($images),
        $status
    ]);

    $_SESSION['success'] = 'Room type added successfully.';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error adding room type. Please try again.';
    error_log($e->getMessage());
}

header('Location: ../room_types.php?hotel_id=' . $hotel_id);
