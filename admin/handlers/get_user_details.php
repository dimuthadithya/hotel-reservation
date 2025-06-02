<?php
header('Content-Type: application/json');
include_once '../../config/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

$user_id = $_GET['id'];

try {
    $sql = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    $isLocked = $user['locked_until'] && strtotime($user['locked_until']) > time();
    $lastLogin = $user['last_login'] ? date('F j, Y H:i', strtotime($user['last_login'])) : 'Never';

    // Format the user details as HTML
    $html = "
        <div class='user-details'>
            <div class='row mb-4'>
                <div class='col-md-12 text-center mb-3'>
                    " . ($user['profile_image'] 
                        ? "<img src='../uploads/img/" . htmlspecialchars($user['profile_image']) . "' 
                            class='rounded-circle' style='width: 100px; height: 100px; object-fit: cover;'
                            alt='" . htmlspecialchars($user['first_name']) . "'>"
                        : "<div class='rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto'
                            style='width: 100px; height: 100px; font-size: 2.5rem;'>
                            " . strtoupper(substr($user['first_name'], 0, 1)) . "
                        </div>") . "
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <h6 class='text-muted mb-3'>Personal Information</h6>
                    <p><strong>Name:</strong> " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "
                        " . ($user['email_verified'] ? "<i class='fas fa-check-circle text-success ms-1' title='Email verified'></i>" : "") . "</p>
                    <p><strong>Phone:</strong> " . htmlspecialchars($user['phone'] ?? 'Not provided') . "</p>
                </div>
                <div class='col-md-6'>
                    <h6 class='text-muted mb-3'>Account Information</h6>
                    <p><strong>Role:</strong> " . ucfirst($user['role']) . "</p>
                    <p><strong>Status:</strong> " . ucfirst($user['account_status']) . "
                        " . ($isLocked ? "<span class='badge bg-danger ms-1'>Locked</span>" : "") . "</p>
                    <p><strong>Last Login:</strong> " . $lastLogin . "</p>
                </div>
            </div>
            <div class='row mt-3'>
                <div class='col-md-6'>
                    <h6 class='text-muted mb-3'>Security Information</h6>
                    <p><strong>Login Attempts:</strong> " . $user['login_attempts'] . "</p>
                    " . ($isLocked ? "<p><strong>Locked Until:</strong> " . date('F j, Y H:i', strtotime($user['locked_until'])) . "</p>" : "") . "
                </div>
                <div class='col-md-6'>
                    <h6 class='text-muted mb-3'>Timestamps</h6>
                    <p><strong>Created:</strong> " . date('F j, Y H:i', strtotime($user['created_at'])) . "</p>
                    <p><strong>Last Updated:</strong> " . date('F j, Y H:i', strtotime($user['updated_at'])) . "</p>
                </div>
            </div>
        </div>";

    echo json_encode(['status' => 'success', 'html' => $html]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
