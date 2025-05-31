<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Hotel Management</h2>
        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addHotelModal">
            <i class="fas fa-plus"></i> Add New Hotel
        </button>
    </div>
    <div class="hotels-list">
        <!-- Hotel items will be loaded dynamically -->
    </div>
</div>

<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Hotel</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addHotelForm" action="./handlers/add_hotel.php" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Hotel Name</label>
                            <input type="text" class="form-control" name="hotel_name" required />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2" required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">District</label>
                            <input type="text" class="form-control" name="district" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Province</label>
                            <input type="text" class="form-control" name="province" required />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Star Rating</label>
                            <select class="form-select" name="star_rating" required>
                                <option value="">Select Rating</option>
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Property Type</label>
                            <select class="form-select" name="property_type" required>
                                <option value="hotel">Hotel</option>
                                <option value="resort">Resort</option>
                                <option value="villa">Villa</option>
                                <option value="homestay">Homestay</option>
                                <option value="guesthouse">Guesthouse</option>
                                <option value="boutique">Boutique</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact Phone</label>
                            <input type="tel" class="form-control" name="contact_phone" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Website URL</label>
                            <input type="url" class="form-control" name="website_url" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Total Rooms</label>
                            <input type="number" class="form-control" name="total_rooms" value="0" min="0" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hotel Photos</label>
                        <input type="file" class="form-control" name="hotel_images[]" multiple accept="image/*" />
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
                <button type="submit" name="add_hotel" class="btn btn-primary" form="addHotelForm">
                    Add Hotel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Hotel Modal -->
<div class="modal fade" id="editHotelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Hotel</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editHotelForm">
                    <input type="hidden" id="editHotelId" />
                    <div class="mb-3">
                        <label class="form-label">Hotel Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="editHotelName"
                            required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                            class="form-control"
                            id="editHotelDescription"
                            rows="3"
                            required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input
                                type="text"
                                class="form-control"
                                id="editHotelLocation"
                                required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="editHotelCategory" required>
                                <option value="">Select Category</option>
                                <option value="luxury">Luxury</option>
                                <option value="business">Business</option>
                                <option value="resort">Resort</option>
                                <option value="boutique">Boutique</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price per Night</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="editHotelPrice"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Count</label>
                            <input
                                type="number"
                                class="form-control"
                                id="editHotelRooms"
                                required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Photos</label>
                        <div class="current-photos row g-2" id="editHotelCurrentPhotos">
                            <!-- Current photos will be loaded here -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Add New Photos</label>
                        <input
                            type="file"
                            class="form-control"
                            multiple
                            accept="image/*" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="editHotelStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Under Maintenance</option>
                        </select>
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
                <button type="submit" class="btn btn-primary" form="editHotelForm">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>