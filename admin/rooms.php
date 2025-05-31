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

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRoomForm" action="handlers/add_room.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Hotel</label>
                        <select class="form-select" name="hotel_id" required>
                            <option value="">Select Hotel</option>
                            <?php
                            $hotels_sql = "SELECT hotel_id, hotel_name FROM hotels WHERE status = 'active' ORDER BY hotel_name";
                            $hotels_stmt = $conn->query($hotels_sql);
                            while ($hotel = $hotels_stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$hotel['hotel_id']}'>{$hotel['hotel_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-select" name="room_type_id" required disabled>
                            <option value="">Select Room Type</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control" name="room_number" required
                            placeholder="e.g., 101, A101, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Floor Number</label>
                        <input type="number" class="form-control" name="floor_number"
                            placeholder="e.g., 1, 2, etc." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="out_of_order">Out of Order</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="addRoomForm">Add Room</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Load room types based on selected hotel
    document.querySelector('select[name="hotel_id"]').addEventListener('change', function() {
        const hotelId = this.value;
        const roomTypeSelect = document.querySelector('select[name="room_type_id"]');

        if (!hotelId) {
            roomTypeSelect.disabled = true;
            roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
            return;
        }

        // Fetch room types for selected hotel
        fetch(`handlers/get_room_types.php?hotel_id=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                roomTypeSelect.disabled = false;
                roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';

                if (data.status === 'success') {
                    data.data.forEach(type => {
                        roomTypeSelect.innerHTML += `<option value="${type.room_type_id}">${type.type_name}</option>`;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading room types');
            });
    });
</script>

<?php include_once 'includes/footer.php'; ?>