<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>
<?php
// Include database connection
require_once '../config/db.php';

// Fetch all amenities
$stmt = $conn->prepare("SELECT * FROM amenities ORDER BY category, amenity_name");
$stmt->execute();
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group amenities by category
$groupedAmenities = [];
foreach ($amenities as $amenity) {
    $groupedAmenities[$amenity['category']][] = $amenity;
}

// Define all possible categories
$allCategories = [
    'basic' => 'Basic Amenities',
    'comfort' => 'Comfort Amenities',
    'business' => 'Business Amenities',
    'recreation' => 'Recreation Amenities',
    'accessibility' => 'Accessibility Features'
];
?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Amenity Management</h2>
        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addAmenityModal">
            <i class="fas fa-plus"></i> Add New Amenity
        </button>
    </div>
    <div class="amenities-list mt-5">
        <div class="row g-4">
            <?php foreach ($allCategories as $categoryKey => $categoryName): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo $categoryName; ?></h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php if (!empty($groupedAmenities[$categoryKey])): ?>
                                <?php foreach ($groupedAmenities[$categoryKey] as $amenity): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas <?php echo htmlspecialchars($amenity['icon_class']); ?> me-2"></i>
                                            <?php echo htmlspecialchars($amenity['amenity_name']); ?>
                                            <?php if (!empty($amenity['description'])): ?>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($amenity['description']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editAmenityModal<?php echo $amenity['amenity_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="handlers/delete_amenity.php" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this amenity?');">
                                                <input type="hidden" name="amenityId" value="<?php echo $amenity['amenity_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Modal for this amenity -->
                                    <div class="modal fade" id="editAmenityModal<?php echo $amenity['amenity_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Amenity</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="handlers/edit_amenity.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="amenityId" value="<?php echo $amenity['amenity_id']; ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Amenity Name</label>
                                                            <input type="text" class="form-control" name="amenityName"
                                                                value="<?php echo htmlspecialchars($amenity['amenity_name']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Icon Class</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">fa-</span>
                                                                <input type="text" class="form-control" name="iconClass"
                                                                    value="<?php echo htmlspecialchars($amenity['icon_class']); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Category</label>
                                                            <select class="form-select" name="category" required>
                                                                <?php foreach ($allCategories as $key => $name): ?>
                                                                    <option value="<?php echo $key; ?>"
                                                                        <?php echo ($key === $amenity['category']) ? 'selected' : ''; ?>>
                                                                        <?php echo $name; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($amenity['description'] ?? ''); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="list-group-item text-muted">No <?php echo strtolower($categoryName); ?> found</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Amenity Modal -->
<div class="modal fade" id="addAmenityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Amenity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="handlers/add_amenity.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amenity Name</label>
                        <input type="text" class="form-control" name="amenityName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text">fa-</span>
                            <input type="text" class="form-control" name="iconClass" placeholder="wifi" required>
                        </div>
                        <small class="text-muted">Enter Font Awesome icon name without 'fa-' prefix</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <?php foreach ($allCategories as $key => $name): ?>
                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Amenity</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>