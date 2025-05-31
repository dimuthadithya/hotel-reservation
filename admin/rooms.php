<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Room Management</h2>
        <div class="header-actions">
            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addRoomModal">
                <i class="fas fa-plus"></i> Add Room
            </button>
            <button
                class="btn btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#addRoomTypeModal">
                <i class="fas fa-list"></i> Manage Room Types
            </button>
        </div>
    </div>
    <div class="room-filters mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="hotelFilter">
                    <option value="">All Hotels</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="roomTypeFilter">
                    <option value="">All Room Types</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="roomStatusFilter">
                    <option value="">All Statuses</option>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="out_of_order">Out of Order</option>
                </select>
            </div>
        </div>
    </div>
    <div class="rooms-list">
        <!-- Room items will be loaded dynamically -->
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>