<?php
include_once 'includes/header.php';
include_once 'includes/sidebar.php';
include_once '../config/db.php';

// Get hotel ID from URL
$hotel_id = isset($_GET['hotel_id']) ? intval($_GET['hotel_id']) : 0;

// Fetch hotel details
$hotel_sql = "SELECT * FROM hotels WHERE hotel_id = ?";
$hotel_stmt = $conn->prepare($hotel_sql);
$hotel_stmt->execute([$hotel_id]);
$hotel = $hotel_stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if hotel not found
if (!$hotel) {
    $_SESSION['error'] = "Hotel not found.";
    header('Location: hotels.php');
    exit;
}

// Fetch room types for this hotel
$types_sql = "SELECT * FROM room_types WHERE hotel_id = ? AND status = 'active' ORDER BY type_name";
$types_stmt = $conn->prepare($types_sql);
$types_stmt->execute([$hotel_id]);
$room_types = $types_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header mb-3">
        <div>
            <h2>Manage Rooms</h2>
            <p class="text-muted">Hotel: <?= htmlspecialchars($hotel['hotel_name']) ?></p>
        </div>
    </div>

    <!-- Add Room Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add New Room</h5>
        </div>
        <div class="card-body">
            <form action="handlers/add_room.php" method="POST" class="row g-3">
                <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">

                <div class="col-md-6">
                    <label class="form-label">Room Type</label> <select class="form-select" name="room_type_id" required>
                        <option value="">Select Room Type</option>
                        <?php if (empty($room_types)): ?>
                            <option value="" disabled>No room types available. Please add room types first.</option>
                        <?php else: ?>
                            <?php foreach ($room_types as $type): ?>
                                <option value="<?= $type['room_type_id'] ?>">
                                    <?= htmlspecialchars($type['type_name']) ?>
                                    (<?= htmlspecialchars($type['bed_type']) ?>, Max: <?= $type['max_occupancy'] ?> guests)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" class="form-control" name="room_number" required
                        placeholder="e.g., 101">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Floor Number</label>
                    <input type="number" class="form-control" name="floor_number" required
                        placeholder="e.g., 1">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="available">Available</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="out_of_order">Out of Order</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Add Room</button>
                    <a href="hotels.php" class="btn btn-secondary">Back to Hotels</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rooms List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Existing Rooms</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Room Number</th>
                            <th>Room Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rooms_sql = "SELECT r.*, rt.type_name 
                                    FROM rooms r 
                                    JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                                    WHERE r.hotel_id = ? 
                                    ORDER BY r.floor_number, r.room_number";
                        $rooms_stmt = $conn->prepare($rooms_sql);
                        $rooms_stmt->execute([$hotel_id]);
                        $rooms = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($rooms) === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center">No rooms added yet.</td>
                            </tr>
                            <?php else:
                            foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['room_number']) ?></td>
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
                                            <form action="handlers/delete_room.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                                                <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this room?')">
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
</div>

<style>
    .action-buttons {
        display: flex;
        gap: 4px;
    }

    .action-buttons .btn {
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

<?php include_once 'includes/footer.php'; ?>