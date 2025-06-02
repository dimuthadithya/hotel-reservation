<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>
<?php include_once '../config/db.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>User Management</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="users-list">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM users ORDER BY created_at DESC";
                    $stmt = $conn->query($sql);
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($users) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                        <?php else:
                        foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <form action="handlers/update_user.php" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Customer</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="handlers/update_user.php" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                        <select name="account_status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                            <option value="active" <?= $user['account_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="suspended" <?= $user['account_status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                            <option value="pending" <?= $user['account_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-sm" title="View User Details">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" title="Edit User">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </button>
                                        <form action="handlers/delete_user.php" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete User" onclick="return confirm('Are you sure you want to delete this user?');">
                                                <i class="fas fa-trash fa-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .action-buttons {
        display: flex;
        gap: 4px;
    }

    .action-buttons .btn,
    .action-buttons form .btn {
        padding: 0.25rem;
        font-size: 0.8rem;
        line-height: 1;
        border-radius: 0.2rem;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons .fas {
        font-size: 0.75rem;
    }

    .table> :not(caption)>*>* {
        padding: 0.4rem 0.5rem;
        vertical-align: middle;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
    }
</style>

<?php include_once 'includes/footer.php'; ?>