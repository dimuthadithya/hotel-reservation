<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Collect form data
        $hotel_data = [
            ':hotel_name' => $_POST['hotel_name'],
            ':description' => $_POST['description'],
            ':address' => $_POST['address'],
            ':district' => $_POST['district'],
            ':province' => $_POST['province'],
            ':star_rating' => $_POST['star_rating'],
            ':property_type' => $_POST['property_type'],
            ':contact_phone' => $_POST['contact_phone'],
            ':contact_email' => $_POST['contact_email'],
            ':website_url' => $_POST['website_url'],
            ':total_rooms' => $_POST['total_rooms'],
            ':status' => $_POST['status'],
            ':main_image' => null
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
            $hotel_data[':main_image'] = $fileName;
        }

        // Insert hotel data
        $sql = "INSERT INTO hotels (hotel_name, description, address, district, province, 
                star_rating, property_type, contact_phone, contact_email, 
                website_url, total_rooms, status, main_image) 
                VALUES (:hotel_name, :description, :address, :district, :province,
                :star_rating, :property_type, :contact_phone, :contact_email,
                :website_url, :total_rooms, :status, :main_image)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($hotel_data);

        $hotel_id = $conn->lastInsertId();

        // Handle image file move after successful database insert
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK && $hotel_data[':main_image']) {
            $uploadDir = "../../uploads/img/hotels/{$hotel_id}";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (!move_uploaded_file($_FILES['main_image']['tmp_name'], "{$uploadDir}/{$hotel_data[':main_image']}")) {
                throw new Exception('Failed to move uploaded file.');
            }
        }
        $conn->commit();

        $_SESSION['success'] = 'Hotel added successfully!';
    } catch (Exception $e) {
        if ($conn) {
            $conn->rollBack();
        }
        $_SESSION['error'] = $e->getMessage();
    }

    header('Location: ../hotels.php');
    exit();
} else {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: ../hotels.php');
    exit();
}
