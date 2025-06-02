<?php
include_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    
    if (!$user_id) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
        } else {
            $_SESSION['error'] = "User ID is required.";
            header('Location: ../users.php');
        }
        exit;
    }

    try {
        $updates = [];
        $params = [':user_id' => $user_id];        // Handle quick updates (status/role)
        if (isset($_POST['account_status'])) {
            // Validate account status
            $valid_statuses = ['active', 'inactive', 'suspended', 'pending'];
            if (!in_array($_POST['account_status'], $valid_statuses)) {
                $_SESSION['error'] = "Invalid account status.";
                header('Location: ../users.php');
                exit;
            }
            $updates[] = "account_status = :account_status";
            $params[':account_status'] = $_POST['account_status'];
        }
        
        if (isset($_POST['role'])) {            // Only admins can change roles
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                $_SESSION['error'] = "You don't have permission to change user roles.";
                header('Location: ../users.php');
                exit;
            }
            
            // Validate role
            $valid_roles = ['admin', 'user'];
            if (!in_array($_POST['role'], $valid_roles)) {
                $_SESSION['error'] = "Invalid role specified.";
                header('Location: ../users.php');
                exit;
            }
            
            // Prevent users from changing their own role
            if ($user_id == $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot change your own role.";
                header('Location: ../users.php');
                exit;
            }
            
            $updates[] = "role = :role";
            $params[':role'] = $_POST['role'];
        }

        // Handle form updates
        if (isset($_POST['first_name'])) {
            $updates[] = "first_name = :first_name";
            $params[':first_name'] = $_POST['first_name'];
        }
        if (isset($_POST['last_name'])) {
            $updates[] = "last_name = :last_name";
            $params[':last_name'] = $_POST['last_name'];
        }
        if (isset($_POST['email'])) {
            $updates[] = "email = :email";
            $params[':email'] = $_POST['email'];
        }
        if (isset($_POST['phone'])) {
            $updates[] = "phone = :phone";
            $params[':phone'] = $_POST['phone'] ?: null;
        }

        if (empty($updates)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['status' => 'error', 'message' => 'No updates provided']);
            } else {
                $_SESSION['error'] = "No updates provided.";
                header('Location: ../users.php');
            }
            exit;
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
            } else {
                $_SESSION['success'] = "User updated successfully.";
                header('Location: ../users.php');
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['status' => 'info', 'message' => 'No changes were made']);
            } else {
                $_SESSION['info'] = "No changes were made.";
                header('Location: ../users.php');
            }
        }
    } catch (PDOException $e) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        } else {
            $_SESSION['error'] = "Error updating user: " . $e->getMessage();
            header('Location: ../users.php');
        }
    }
    exit;
}

header('Location: ../users.php');
exit;
