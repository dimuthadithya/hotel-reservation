<?php
include_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    
    if (!empty($user_id)) {
        try {
            // First, check if user exists and is not the last admin
            $check_sql = "SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin'";
            $check_stmt = $conn->query($check_sql);
            $admin_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['admin_count'];

            $user_sql = "SELECT role FROM users WHERE user_id = :user_id";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->execute([':user_id' => $user_id]);
            $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['role'] === 'admin' && $admin_count <= 1) {
                header('Location: ../users.php?error=Cannot delete the last administrator');
                exit;
            }

            // Check if user has any bookings
            $booking_sql = "SELECT COUNT(*) as booking_count FROM bookings WHERE user_id = :user_id";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->execute([':user_id' => $user_id]);
            $has_bookings = $booking_stmt->fetch(PDO::FETCH_ASSOC)['booking_count'] > 0;

            if ($has_bookings) {
                header('Location: ../users.php?error=Cannot delete user with existing bookings');
                exit;
            }

            // If all checks pass, delete the user
            $delete_sql = "DELETE FROM users WHERE user_id = :user_id";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->execute([':user_id' => $user_id]);

            if ($delete_stmt->rowCount() > 0) {
                header('Location: ../users.php?success=User deleted successfully');
                exit;
            } else {
                header('Location: ../users.php?error=User not found');
                exit;
            }
        } catch (PDOException $e) {
            header('Location: ../users.php?error=Failed to delete user');
            exit;
        }
    } else {
        header('Location: ../users.php?error=Invalid user ID');
        exit;
    }
} else {
    header('Location: ../users.php');
    exit;
}
