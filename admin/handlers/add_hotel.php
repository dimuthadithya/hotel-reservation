<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hotel'])) {
    try {
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
            ':status' => $_POST['status']
        ];
        // Insert hotel data
        $sql = "INSERT INTO hotels (hotel_name, description, address, district, province, 
                star_rating, property_type, contact_phone, contact_email, 
                website_url, total_rooms, status) 
                VALUES (:hotel_name, :description, :address, :district, :province,
                :star_rating, :property_type, :contact_phone, :contact_email,
                :website_url, :total_rooms, :status)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($hotel_data);
        $_SESSION['success'] = "Hotel added successfully!";
        header("Location: ../hotels.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: ../hotels.php");
        exit();
    }
} else {
    header("Location: ../hotels.php");
    exit();
}
