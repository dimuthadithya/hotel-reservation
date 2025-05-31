<?php

require_once '../config/db.php';
session_start();
// Check if the form is submitted

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $hotel_name = $_POST['hotel_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $address = $_POST['address'] ?? '';
    $district = $_POST['district'] ?? '';
    $province = $_POST['province'] ?? '';
    $star_rating = $_POST['star_rating'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $website_url = $_POST['website_url'] ?? '';
    $total_rooms = $_POST['total_rooms'] ?? 0;
    $property_type = $_POST['property_type'] ?? 'hotel';
    $status = $_POST['status'] ?? 'pending';

    // Validate required inputs
    if (empty($hotel_name) || empty($description) || empty($address) || empty($district) || empty($province)) {
        $_SESSION['error'] = "Required fields cannot be empty";
        header('Location: ../admin/index.php#hotels');
        exit;
    }

    try {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO hotels (
            hotel_name, description, address, district, province, 
            star_rating, contact_phone, contact_email, website_url, 
            total_rooms, property_type, status
        ) VALUES (
            :hotel_name, :description, :address, :district, :province,
            :star_rating, :contact_phone, :contact_email, :website_url,
            :total_rooms, :property_type, :status
        )");

        $stmt->bindParam(':hotel_name', $hotel_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':star_rating', $star_rating);
        $stmt->bindParam(':contact_phone', $contact_phone);
        $stmt->bindParam(':contact_email', $contact_email);
        $stmt->bindParam(':website_url', $website_url);
        $stmt->bindParam(':total_rooms', $total_rooms);
        $stmt->bindParam(':property_type', $property_type);
        $stmt->bindParam(':status', $status);
        if ($stmt->execute()) {
            // Handle image uploads if there are any
            if (!empty($_FILES['hotel_images'])) {
                $hotel_id = $conn->lastInsertId();
                $upload_dir = "../uploads/img/hotels/";

                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                foreach ($_FILES['hotel_images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['hotel_images']['name'][$key];
                    $file_tmp = $_FILES['hotel_images']['tmp_name'][$key];

                    // Generate unique filename
                    $unique_filename = uniqid() . '_' . $file_name;
                    $target_file = $upload_dir . $unique_filename;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        // Insert image record into hotel_images table
                        $img_stmt = $conn->prepare("INSERT INTO hotel_images (hotel_id, image_url, image_type) VALUES (:hotel_id, :image_url, 'gallery')");
                        $relative_path = "assets/img/hotels/" . $unique_filename;
                        $img_stmt->execute([
                            ':hotel_id' => $hotel_id,
                            ':image_url' => $relative_path
                        ]);
                    }
                }
            }

            $_SESSION['success'] = "Hotel added successfully";
            header('Location: ../admin/index.php#hotels');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding hotel: " . $e->getMessage();
        header('Location: ../admin/index.php#hotels');
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request method";
    header('Location: ../admin/index.php#hotels');
    exit;
}
