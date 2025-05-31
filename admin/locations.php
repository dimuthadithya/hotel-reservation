<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Location Management</h2>
        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addLocationModal">
            <i class="fas fa-plus"></i> Add New Location
        </button>
    </div>
    <div class="locations-list">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Location Name</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Popular</th>
                        <th>Hotels</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Location items will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Location</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addLocationForm">
                    <div class="mb-3">
                        <label class="form-label">Location Name</label>
                        <input
                            type="text"
                            class="form-control"
                            name="locationName"
                            required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">District</label>
                        <input
                            type="text"
                            class="form-control"
                            name="district"
                            required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Province</label>
                        <input
                            type="text"
                            class="form-control"
                            name="province"
                            required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                            class="form-control"
                            name="description"
                            rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location Image</label>
                        <input
                            type="file"
                            class="form-control"
                            name="imageUrl"
                            accept="image/*" />
                    </div>
                    <div class="form-check mb-3">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            name="isPopular"
                            id="isPopular" />
                        <label class="form-check-label" for="isPopular">Mark as Popular Location</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>
                <button
                    type="submit"
                    class="btn btn-primary"
                    form="addLocationForm">
                    Add Location
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>