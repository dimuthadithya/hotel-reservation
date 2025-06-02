<?php
header('Content-Type: application/json');
include_once '../../config/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

$user_id = $_GET['id'];

try {
    $sql = "SELECT user_id, first_name, last_name, email, phone, role 
            FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    // Format the user edit form as HTML
    $html = "
        <form id='editUserForm'>
            <input type='hidden' name='user_id' value='" . $user['user_id'] . "'>
            <div class='row g-3'>
                <div class='col-md-6'>
                    <label for='firstName' class='form-label'>First Name</label>
                    <input type='text' class='form-control' id='firstName' name='first_name' 
                        value='" . htmlspecialchars($user['first_name']) . "' required>
                </div>
                <div class='col-md-6'>
                    <label for='lastName' class='form-label'>Last Name</label>
                    <input type='text' class='form-control' id='lastName' name='last_name' 
                        value='" . htmlspecialchars($user['last_name']) . "' required>
                </div>
                <div class='col-md-12'>
                    <label for='email' class='form-label'>Email</label>
                    <input type='email' class='form-control' id='email' name='email' 
                        value='" . htmlspecialchars($user['email']) . "' required>
                </div>
                <div class='col-md-12'>
                    <label for='phone' class='form-label'>Phone</label>
                    <input type='tel' class='form-control' id='phone' name='phone' 
                        value='" . htmlspecialchars($user['phone'] ?? '') . "'
                        pattern='[0-9+()-\s]*'>
                    <div class='form-text'>Enter phone number in any format (optional)</div>
                </div>
            </div>
        </form>";

    echo json_encode(['status' => 'success', 'html' => $html]);
    exit;
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
