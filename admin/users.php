<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>User Management</h2>
        <div class="header-actions d-flex gap-2">
            <select class="form-select" id="userRoleFilter">
                <option value="">All Roles</option>
                <option value="customer">Customers</option>
                <option value="admin">Administrators</option>
                <option value="moderator">Moderators</option>
                <option value="hotel_manager">Hotel Managers</option>
            </select>
            <input
                type="search"
                class="form-control"
                placeholder="Search users..." />
        </div>
    </div>
    <div class="users-list">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- User items will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>