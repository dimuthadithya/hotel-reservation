<?php
require_once '../../config/db.php';

try {
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $stmt = $conn->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [];
    foreach ($users as $user) {
        $response[] = [
            'user_id' => $user['user_id'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'status' => $user['account_status'],
            'joined' => $user['created_at']
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $response]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
}
