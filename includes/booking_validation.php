<?php

function validateBookingData($data)
{
    $errors = [];

    // Validate required fields
    $required_fields = [
        'room_type_id' => 'Room type',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'email' => 'Email',
        'phone' => 'Phone number',
        'check_in' => 'Check-in date',
        'check_out' => 'Check-out date',
        'adults' => 'Number of adults'
    ];

    foreach ($required_fields as $field => $label) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = "$label is required";
        }
    }

    // If required fields are missing, return early
    if (!empty($errors)) {
        return ['valid' => false, 'errors' => $errors];
    }    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate dates
    $check_in_date = new DateTime($data['check_in']);
    $check_out_date = new DateTime($data['check_out']);
    $today = new DateTime();

    if ($check_in_date < $today) {
        $errors[] = "Check-in date cannot be in the past";
    }

    if ($check_out_date <= $check_in_date) {
        $errors[] = "Check-out date must be after check-in date";
    }

    // Validate adults count
    $adults = filter_var($data['adults'], FILTER_VALIDATE_INT);
    if ($adults === false || $adults < 1) {
        $errors[] = "Number of adults must be at least 1";
    }
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'sanitized_data' => [
            'room_type_id' => filter_var($data['room_type_id'], FILTER_SANITIZE_NUMBER_INT),
            'first_name' => htmlspecialchars(trim($data['first_name']), ENT_QUOTES, 'UTF-8'),
            'last_name' => htmlspecialchars(trim($data['last_name']), ENT_QUOTES, 'UTF-8'),
            'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            'phone' => htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8'),
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'adults' => $adults,
            'children' => isset($data['children']) ? filter_var($data['children'], FILTER_SANITIZE_NUMBER_INT) : 0,
            'special_requests' => isset($data['special_requests']) ?
                htmlspecialchars(trim($data['special_requests']), ENT_QUOTES, 'UTF-8') : ''
        ]
    ];
}
