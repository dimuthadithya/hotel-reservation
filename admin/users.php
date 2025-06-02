<?php 
include_once 'includes/header.php';
include_once 'includes/sidebar.php';
include_once '../config/db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied. You must be an administrator to view this page.";
    header('Location: ../login.php');
    exit;
}
?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header d-flex justify-content-between align-items-center">
        <h2>User Management</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="users-list">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
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
                            <td colspan="8" class="text-center">No users found.</td>
                        </tr>
                        <?php else:
                        foreach ($users as $user): 
                            $lastLogin = $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never';
                            $isLocked = $user['locked_until'] && strtotime($user['locked_until']) > time();
                            ?>
                            <tr>
                                <td class="d-flex align-items-center gap-2">
                                    <?php if ($user['profile_image']): ?>
                                        <img src="../uploads/img/<?= htmlspecialchars($user['profile_image']) ?>" 
                                            class="rounded-circle" width="32" height="32" 
                                            alt="<?= htmlspecialchars($user['first_name']) ?>">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                            style="width: 32px; height: 32px;">
                                            <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($user['email']) ?>
                                    <?php if ($user['email_verified']): ?>
                                        <i class="fas fa-check-circle text-success" title="Email verified"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></td>                                <td>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): // Only admins can change roles ?>
                                        <form action="handlers/update_user.php" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                            <input type="hidden" name="current_role" value="<?= $user['role'] ?>">
                                            <select name="role" class="form-select form-select-sm" 
                                                onchange="confirmRoleChange(this, <?= $user['user_id'] ?>)" 
                                                style="width: auto;"
                                                <?= $user['user_id'] === $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Customer</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="handlers/update_user.php" method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                        <select name="account_status" class="form-select form-select-sm" 
                                            onchange="this.form.submit()" 
                                            style="width: auto;"
                                            <?= $isLocked ? 'disabled' : '' ?>>
                                            <option value="active" <?= $user['account_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= $user['account_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            <option value="suspended" <?= $user['account_status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                            <option value="pending" <?= $user['account_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        </select>
                                        <?php if ($isLocked): ?>
                                            <div class="small text-danger">Locked until: <?= date('M d, Y H:i', strtotime($user['locked_until'])) ?></div>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td>
                                    <?= $lastLogin ?>
                                    <?php if ($user['login_attempts'] > 0): ?>
                                        <span class="badge bg-warning" title="Failed login attempts">
                                            <?= $user['login_attempts'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-sm" title="View User Details" onclick="viewUser(<?= $user['user_id'] ?>)">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" title="Edit User" onclick="editUser(<?= $user['user_id'] ?>)">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </button>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <form action="handlers/delete_user.php" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete User">
                                                    <i class="fas fa-trash fa-sm"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editUserContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitUserEdit()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function viewUser(userId) {
        try {
            const response = await fetch(`handlers/get_user_details.php?id=${userId}`);
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            if (data.status === 'success') {
                document.getElementById('userDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
            } else {
                alert('Failed to load user details: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load user details: ' + error.message);
        }
    }

    async function editUser(userId) {
        try {
            const response = await fetch(`handlers/get_user_edit.php?id=${userId}`);
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            if (data.status === 'success') {
                document.getElementById('editUserContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            } else {
                alert('Failed to load user edit form: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load user edit form: ' + error.message);
        }
    }

    async function submitUserEdit() {
        const form = document.getElementById('editUserForm');
        if (!form) {
            alert('Error: Form not found');
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch('handlers/update_user.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            if (data.status === 'success') {
                // Close the modal before reloading
                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                if (modal) modal.hide();
                
                location.reload();
            } else {
                alert(data.message || 'Failed to update user');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update user: ' + error.message);
        }
    }

    function confirmDelete(event) {
        if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    function confirmRoleChange(selectElement, userId) {
        const newRole = selectElement.value;
        const currentRole = selectElement.form.querySelector('input[name="current_role"]').value;
        
        if (newRole === currentRole) return; // No change

        const message = newRole === 'admin' 
            ? 'Are you sure you want to give this user admin privileges?' 
            : 'Are you sure you want to remove admin privileges from this user?';

        if (confirm(message)) {
            selectElement.form.submit();
        } else {
            // Reset to previous value if cancelled
            selectElement.value = currentRole;
        }
    }
</script>

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