<?php
include_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $new_role = $_POST['role'] ?? '';

    if (!empty($user_id) && !empty($new_role)) {
        try {
            $sql = "UPDATE users SET role = :role WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':role' => $new_role,
                ':user_id' => $user_id
            ]);

            header('Location: ../users.php?success=Role updated successfully');
            exit;
        } catch (PDOException $e) {
            header('Location: ../users.php?error=Failed to update role');
            exit;
        }
    } else {
        header('Location: ../users.php?error=Invalid parameters');
        exit;
    }
} else {
    header('Location: ../users.php');
    exit;
}
