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
    <div class="content-header mb-3">
        <div>
            <h2>Room Management</h2>
            <?php if ($hotel_name): ?>
                <p class="text-muted">Managing rooms for: <?= htmlspecialchars($hotel_name) ?></p>
            <?php endif; ?>
        </div>
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
                                    <form action="handlers/delete_room.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Room">
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