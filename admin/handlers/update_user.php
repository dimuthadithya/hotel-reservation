<?php
include_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $account_status = $_POST['account_status'] ?? null;
    $role = $_POST['role'] ?? null;

    if (!$user_id || (!$account_status && !$role)) {
        $_SESSION['error'] = "Invalid request parameters.";
        header('Location: ../users.php');
        exit;
    }

    try {
        $updates = [];
        $params = [':user_id' => $user_id];

        if ($account_status) {
            $updates[] = "account_status = :account_status";
            $params[':account_status'] = $account_status;
        }

        if ($role) {
            $updates[] = "role = :role";
            $params[':role'] = $role;
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "User updated successfully.";
        } else {
            $_SESSION['error'] = "No changes were made.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating user: " . $e->getMessage();
    }

    header('Location: ../users.php');
    exit;
}

header('Location: ../users.php');
exit;
