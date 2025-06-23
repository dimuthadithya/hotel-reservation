<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../bookings.php');
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bookings_export_' . date('Y-m-d') . '.csv"');

// Create file pointer connected to PHP output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Add header row
fputcsv($output, [
    'Booking Reference',
    'Hotel',
    'Guest Name',
    'Guest Email',
    'Guest Phone',
    'Room Type',
    'Room Number',
    'Check In',
    'Check Out',
    'Adults',
    'Children',
    'Total Nights',
    'Room Rate',
    'Total Amount',
    'Status',
    'Created At'
]);

// Build query with filters
$params = [];
$where_conditions = [];

if (!empty($_GET['hotel_id'])) {
    $where_conditions[] = "b.hotel_id = ?";
    $params[] = $_GET['hotel_id'];
}

if (!empty($_GET['status'])) {
    $where_conditions[] = "b.booking_status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['from_date'])) {
    $where_conditions[] = "b.check_in_date >= ?";
    $params[] = $_GET['from_date'];
}

if (!empty($_GET['to_date'])) {
    $where_conditions[] = "b.check_out_date <= ?";
    $params[] = $_GET['to_date'];
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$sql = "SELECT b.*, h.hotel_name, rt.type_name as room_type, r.room_number 
        FROM bookings b 
        JOIN hotels h ON b.hotel_id = h.hotel_id 
        JOIN room_types rt ON b.room_type_id = rt.room_type_id
        JOIN room_bookings rb ON b.booking_id = rb.booking_id
        JOIN rooms r ON rb.room_id = r.room_id
        $where_clause
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

// Output each row
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['booking_reference'],
        $row['hotel_name'],
        $row['guest_name'],
        $row['guest_email'],
        $row['guest_phone'],
        $row['room_type'],
        $row['room_number'],
        $row['check_in_date'],
        $row['check_out_date'],
        $row['adults'],
        $row['children'],
        $row['total_nights'],
        $row['room_rate'],
        $row['total_amount'],
        $row['booking_status'],
        $row['created_at']
    ]);
}

fclose($output);
