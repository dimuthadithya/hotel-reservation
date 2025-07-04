<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>
<?php include_once '../config/db.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>User Management</h2>
    </div>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                        <th width="150">Actions</th>
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
                                    <form action="handlers/update_role.php" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 100px;">
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td><span class="badge bg-<?= $user['account_status'] === 'active' ? 'success' : ($user['account_status'] === 'pending' ? 'warning' : 'danger') ?>"><?= ucfirst($user['account_status']) ?></span></td>
                                <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewUserModal<?= $user['user_id'] ?>">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </button>
                                        <form action="handlers/delete_user.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                                                <i class="fas fa-trash fa-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <!-- User View Modal -->
                            <div class="modal fade" id="viewUserModal<?= $user['user_id'] ?>" tabindex="-1" aria-labelledby="viewUserModalLabel<?= $user['user_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewUserModalLabel<?= $user['user_id'] ?>">User Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <strong>Name:</strong>
                                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Email:</strong>
                                                <?= htmlspecialchars($user['email']) ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Role:</strong>
                                                <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Status:</strong>
                                                <?= ucfirst(htmlspecialchars($user['account_status'])) ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Phone:</strong>
                                                <?= htmlspecialchars($user['phone'] ?? 'Not provided') ?>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Joined:</strong>
                                                <?= date('F j, Y', strtotime($user['created_at'])) ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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