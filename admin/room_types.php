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

// Fetch existing room types
$types_sql = "SELECT * FROM room_types WHERE hotel_id = ? ORDER BY type_name";
$types_stmt = $conn->prepare($types_sql);
$types_stmt->execute([$hotel_id]);
$room_types = $types_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header mb-3">
        <div>
            <h2>Room Types</h2>
            <p class="text-muted">Hotel: <?= htmlspecialchars($hotel['hotel_name']) ?></p>
        </div>
        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addRoomTypeModal">
            <i class="fas fa-plus"></i> Add Room Type
        </button>
    </div>

    <?php include 'includes/alert_message.php'; ?>

    <!-- Room Types List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type Name</th>
                            <th>Description</th>
                            <th>Max Occupancy</th>
                            <th>Base Price</th>
                            <th>Room Size</th>
                            <th>Total Rooms</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($room_types)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No room types found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($room_types as $type): ?>
                                <tr>
                                    <td><?= htmlspecialchars($type['type_name']) ?></td>
                                    <td>
                                        <?= strlen($type['description']) > 50 ?
                                            htmlspecialchars(substr($type['description'], 0, 50)) . '...' :
                                            htmlspecialchars($type['description']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($type['max_occupancy']) ?></td>
                                    <td>LKR <?= number_format($type['base_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($type['room_size']) ?></td>
                                    <td><?= htmlspecialchars($type['total_rooms']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $type['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($type['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="editRoomType(<?= $type['room_type_id'] ?>)"
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit fa-sm"></i>
                                            </button>
                                            <form action="handlers/delete_room_type.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="room_type_id" value="<?= $type['room_type_id'] ?>">
                                                <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this room type?')">
                                                    <i class="fas fa-trash fa-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Type Modal -->
<div class="modal fade" id="addRoomTypeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRoomTypeForm" action="handlers/add_room_type.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type Name</label>
                            <input type="text" class="form-control" name="type_name" required
                                placeholder="e.g., Deluxe Room, Suite, etc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Base Price (LKR)</label>
                            <input type="number" class="form-control" name="base_price" required min="0" step="0.01"
                                placeholder="Enter base price per night">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required
                                placeholder="Describe the room type and its features"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Occupancy</label>
                            <input type="number" class="form-control" name="max_occupancy" required min="1"
                                placeholder="Maximum guests allowed">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room Size</label>
                            <input type="text" class="form-control" name="room_size" required
                                placeholder="e.g., 30 sqm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Rooms</label>
                            <input type="number" class="form-control" name="total_rooms" required min="0"
                                placeholder="Number of rooms">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bed Type</label>
                            <input type="text" class="form-control" name="bed_type" required
                                placeholder="e.g., King, Twin, etc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Room Amenities</label>
                            <div class="border rounded p-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="wifi">
                                            <label class="form-check-label">WiFi</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="tv">
                                            <label class="form-check-label">TV</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="ac">
                                            <label class="form-check-label">Air Conditioning</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="minibar">
                                            <label class="form-check-label">Minibar</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="safe">
                                            <label class="form-check-label">Safe</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="bathtub">
                                            <label class="form-check-label">Bathtub</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="balcony">
                                            <label class="form-check-label">Balcony</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="coffee">
                                            <label class="form-check-label">Coffee Maker</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="desk">
                                            <label class="form-check-label">Work Desk</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Room Images</label>
                            <input type="file" class="form-control" name="room_images[]" multiple accept="image/*">
                            <small class="text-muted">You can select multiple images. Maximum 5 images allowed.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="addRoomTypeForm">Add Room Type</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Room Type Modal -->
<div class="modal fade" id="editRoomTypeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Room Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editRoomTypeForm" action="handlers/edit_room_type.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="room_type_id" id="edit_room_type_id">
                    <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type Name</label>
                            <input type="text" class="form-control" name="type_name" id="edit_type_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Base Price (LKR)</label>
                            <input type="number" class="form-control" name="base_price" id="edit_base_price" required min="0" step="0.01">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Max Occupancy</label>
                            <input type="number" class="form-control" name="max_occupancy" id="edit_max_occupancy" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room Size</label>
                            <input type="text" class="form-control" name="room_size" id="edit_room_size" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Rooms</label>
                            <input type="number" class="form-control" name="total_rooms" id="edit_total_rooms" required min="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bed Type</label>
                            <input type="text" class="form-control" name="bed_type" id="edit_bed_type" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Room Amenities</label>
                            <div class="border rounded p-3">
                                <div class="row" id="edit_amenities_container">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Current Images</label>
                            <div class="border rounded p-3" id="current_images_container">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Add New Images</label>
                            <input type="file" class="form-control" name="room_images[]" multiple accept="image/*">
                            <small class="text-muted">You can select multiple images. Maximum 5 new images allowed.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="editRoomTypeForm">Save Changes</button>
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

    #current_images_container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .room-image-container {
        position: relative;
    }

    .room-image-container img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .remove-image:hover {
        background: rgba(255, 0, 0, 0.1);
    }
</style>

<script>
    function editRoomType(roomTypeId) {
        // Clear previous form data
        document.getElementById('edit_amenities_container').innerHTML = '';
        document.getElementById('current_images_container').innerHTML = '';

        // Fetch room type details
        fetch(`handlers/get_room_type.php?id=${roomTypeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const roomType = data.data;

                    // Fill the form with room type data
                    document.getElementById('edit_room_type_id').value = roomType.room_type_id;
                    document.getElementById('edit_type_name').value = roomType.type_name;
                    document.getElementById('edit_description').value = roomType.description;
                    document.getElementById('edit_max_occupancy').value = roomType.max_occupancy;
                    document.getElementById('edit_base_price').value = roomType.base_price;
                    document.getElementById('edit_room_size').value = roomType.room_size;
                    document.getElementById('edit_bed_type').value = roomType.bed_type;
                    document.getElementById('edit_total_rooms').value = roomType.total_rooms;
                    document.getElementById('edit_status').value = roomType.status;

                    // Set amenities
                    const amenities = JSON.parse(roomType.room_amenities || '[]');
                    const amenitiesHtml = `
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="wifi" ${amenities.includes('wifi') ? 'checked' : ''}>
                            <label class="form-check-label">WiFi</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="tv" ${amenities.includes('tv') ? 'checked' : ''}>
                            <label class="form-check-label">TV</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="ac" ${amenities.includes('ac') ? 'checked' : ''}>
                            <label class="form-check-label">Air Conditioning</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="minibar" ${amenities.includes('minibar') ? 'checked' : ''}>
                            <label class="form-check-label">Minibar</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="safe" ${amenities.includes('safe') ? 'checked' : ''}>
                            <label class="form-check-label">Safe</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="bathtub" ${amenities.includes('bathtub') ? 'checked' : ''}>
                            <label class="form-check-label">Bathtub</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="balcony" ${amenities.includes('balcony') ? 'checked' : ''}>
                            <label class="form-check-label">Balcony</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="coffee" ${amenities.includes('coffee') ? 'checked' : ''}>
                            <label class="form-check-label">Coffee Maker</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="desk" ${amenities.includes('desk') ? 'checked' : ''}>
                            <label class="form-check-label">Work Desk</label>
                        </div>
                    </div>
                `;
                    document.getElementById('edit_amenities_container').innerHTML = amenitiesHtml;

                    // Display current images
                    const images = JSON.parse(roomType.images || '[]');
                    const imagesHtml = images.map((image, index) => `
                    <div class="room-image-container">
                        <img src="${image}" alt="Room Image ${index + 1}">
                        <button type="button" class="remove-image" onclick="removeImage(${roomType.room_type_id}, ${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `).join('');
                    document.getElementById('current_images_container').innerHTML = imagesHtml;

                    // Show the modal
                    const editModal = new bootstrap.Modal(document.getElementById('editRoomTypeModal'));
                    editModal.show();
                } else {
                    alert('Error loading room type details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading room type details');
            });
    }

    function removeImage(roomTypeId, imageIndex) {
        if (confirm('Are you sure you want to remove this image?')) {
            fetch('handlers/remove_room_type_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        room_type_id: roomTypeId,
                        image_index: imageIndex
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Remove the image container from the DOM
                        const imageContainers = document.getElementById('current_images_container').children;
                        imageContainers[imageIndex].remove();
                    } else {
                        alert('Error removing image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing image');
                });
        }
    }
</script>

<?php include_once 'includes/footer.php'; ?>