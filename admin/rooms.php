<?php
include_once 'includes/header.php';
include_once 'includes/sidebar.php';
include_once '../config/db.php';

// Get selected hotel ID from URL
$selected_hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : null;

// Get hotel details if hotel_id is provided
$hotel_name = "";
if ($selected_hotel_id) {
    $hotel_sql = "SELECT hotel_name FROM hotels WHERE hotel_id = ?";
    $hotel_stmt = $conn->prepare($hotel_sql);
    $hotel_stmt->execute([$selected_hotel_id]);
    $hotel = $hotel_stmt->fetch(PDO::FETCH_ASSOC);
    $hotel_name = $hotel ? $hotel['hotel_name'] : "";
}
?>

<!-- Main Content -->
<div class="admin-main">
    <?php include 'includes/alert_message.php'; ?>

    <!-- Alert Container (for JavaScript alerts) -->
    <div id="alertContainer" class="alert-container mb-3"></div>

    <div class="content-header mb-3">
        <div>
            <h2>Room Management</h2>
            <?php if ($hotel_name): ?>
                <p class="text-muted">Managing rooms for: <?= htmlspecialchars($hotel_name) ?></p>
            <?php endif; ?>
        </div>
        <div class="header-actions"> <a href="room_types.php" class="btn btn-primary">
                <i class="fas fa-list"></i> Manage Room Types
            </a>
        </div>
    </div>

    <div class="room-filters mb-3">
        <div class="row g-3">
            <?php if (!$selected_hotel_id): ?>
                <div class="col-md-3">
                    <select class="form-select" id="hotelFilter">
                        <option value="">All Hotels</option>
                        <?php
                        $hotels_sql = "SELECT hotel_id, hotel_name FROM hotels WHERE status = 'active' ORDER BY hotel_name";
                        $hotels_stmt = $conn->query($hotels_sql);
                        while ($hotel = $hotels_stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$hotel['hotel_id']}'>{$hotel['hotel_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <select class="form-select" id="roomTypeFilter">
                    <option value="">All Room Types</option>
                    <?php
                    $type_sql = "SELECT room_type_id, type_name FROM room_types " .
                        ($selected_hotel_id ? "WHERE hotel_id = ? " : "") .
                        "ORDER BY type_name";
                    $type_stmt = $conn->prepare($type_sql);
                    $type_stmt->execute($selected_hotel_id ? [$selected_hotel_id] : []);
                    while ($type = $type_stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$type['room_type_id']}'>{$type['type_name']}</option>";
                    }
                    ?>
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

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Room Number</th>
                    <?php if (!$selected_hotel_id): ?>
                        <th>Hotel</th>
                    <?php endif; ?>
                    <th>Room Type</th>
                    <th>Floor</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT r.*, h.hotel_name, rt.type_name 
                        FROM rooms r 
                        JOIN hotels h ON r.hotel_id = h.hotel_id 
                        JOIN room_types rt ON r.room_type_id = rt.room_type_id
                        WHERE 1=1 " .
                    ($selected_hotel_id ? "AND r.hotel_id = ? " : "") .
                    "ORDER BY r.created_at DESC";

                $stmt = $conn->prepare($sql);
                $stmt->execute($selected_hotel_id ? [$selected_hotel_id] : []);
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($rooms) === 0): ?>
                    <tr>
                        <td colspan="<?= $selected_hotel_id ? '5' : '6' ?>" class="text-center">No rooms found.</td>
                    </tr>
                    <?php else:
                    foreach ($rooms as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['room_number']) ?></td>
                            <?php if (!$selected_hotel_id): ?>
                                <td><?= htmlspecialchars($room['hotel_name']) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($room['type_name']) ?></td>
                            <td><?= htmlspecialchars($room['floor_number']) ?></td>
                            <td>
                                <span class="badge bg-<?=
                                                        $room['status'] === 'available' ? 'success' : ($room['status'] === 'occupied' ? 'warning' : ($room['status'] === 'maintenance' ? 'info' : 'secondary'))
                                                        ?>">
                                    <?= ucfirst($room['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button onclick="editRoom(<?= $room['room_id'] ?>)" class="btn btn-warning btn-sm" title="Edit Room">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>
                                    <button onclick="deleteRoom(<?= $room['room_id'] ?>, '<?= htmlspecialchars($room['room_number']) ?>')" class="btn btn-danger btn-sm" title="Delete Room">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                <?php endforeach;
                endif; ?>
            </tbody>
        </table>
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

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
    }
</style>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <form id="editRoomForm" action="handlers/edit_room.php" method="POST">
                    <input type="hidden" name="room_id" id="edit_room_id">

                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-select" name="room_type_id" id="edit_room_type_id" required>
                            <option value="">Select Room Type</option>
                            <!-- Will be populated by JavaScript -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control" name="room_number" id="edit_room_number" required
                            placeholder="e.g., 101, A101, etc.">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Floor Number</label>
                        <input type="number" class="form-control" name="floor_number" id="edit_floor_number"
                            placeholder="e.g., 1, 2, etc." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="out_of_order">Out of Order</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="editRoomForm">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.innerHTML = ''; // Clear previous alerts
        alertContainer.appendChild(alertDiv);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    } // Function to handle room edit
    function editRoom(roomId) {
        // Reset form validation state
        const form = document.getElementById('editRoomForm');
        form.classList.remove('was-validated');

        // Store the original form HTML
        const originalFormHtml = document.getElementById('editModalBody').innerHTML;

        // Show loading state
        document.getElementById('editModalBody').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        // Show the modal first
        const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
        editModal.show();

        // Fetch room details
        fetch(`handlers/get_room.php?room_id=${roomId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') { // Restore the form HTML first
                    document.getElementById('editModalBody').innerHTML = originalFormHtml;

                    // Populate room types dropdown
                    const roomTypeSelect = document.getElementById('edit_room_type_id');
                    roomTypeSelect.innerHTML = '<option value="">Select Room Type</option>';
                    data.room_types.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.room_type_id;
                        option.textContent = type.type_name;
                        roomTypeSelect.appendChild(option);
                    });

                    // Then populate form with room data
                    document.getElementById('edit_room_id').value = data.room.room_id;
                    document.getElementById('edit_room_number').value = data.room.room_number;
                    document.getElementById('edit_room_type_id').value = data.room.room_type_id;
                    document.getElementById('edit_floor_number').value = data.room.floor_number;
                    document.getElementById('edit_status').value = data.room.status;
                } else {
                    showAlert('danger', 'Error loading room details: ' + data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Error loading room details: ' + error.message);
            });
    }    // Function to handle room deletion
    function deleteRoom(roomId, roomNumber) {
        if (confirm(`Are you sure you want to delete Room ${roomNumber}? This action cannot be undone.`)) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'handlers/delete_room.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'room_id';
            input.value = roomId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Handle edit form submission
    document.getElementById('editRoomForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Form validation
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        const formData = new FormData(this); // Submit the form directly - no need for fetch
        this.submit();
    });
</script>

<?php include_once 'includes/footer.php'; ?>