<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>
<?php include_once '../config/db.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header mb-3">
        <h2>Hotel Management</h2>
        <button
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addHotelModal">
            <i class="fas fa-plus"></i> Add New Hotel
        </button>
    </div>
    <div class="hotels-list">
        <?php include 'includes/hotels_list.php'; ?>
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
                        <div class="col-md-8">
                            <label class="form-label">Main Image</label>
                            <input type="file" class="form-control" name="main_image" accept="image/*" required onchange="previewImage(this, 'mainImagePreview')" />
                            <div class="form-text">Recommended size: 1200x800 pixels, max 2MB</div>
                        </div>
                        <div class="col-md-4">
                            <img id="mainImagePreview" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                class="img-thumbnail mt-2" style="width: 100%; height: 150px; object-fit: cover;" />
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
                </form>
            </div>
            <div class="modal-footer"> <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" form="addHotelForm">
                    <i class="fas fa-plus"></i> Add Hotel
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="hotelTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Hotel Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="amenities-tab" data-bs-toggle="tab" data-bs-target="#amenities" type="button" role="tab">Amenities</button>
                    </li>
                </ul>

                <div class="tab-content" id="hotelTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <form id="editHotelForm" method="POST" action="handlers/update_hotel.php" enctype="multipart/form-data">
                            <input type="hidden" name="hotel_id" id="edit_hotel_id" />
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Hotel Name</label>
                                    <input type="text" class="form-control" name="hotel_name" id="edit_hotel_name" required />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">Main Image</label>
                                    <input type="file" class="form-control" name="main_image" accept="image/*" onchange="previewImage(this, 'editImagePreview')" />
                                    <div class="form-text">Leave empty to keep current image. Recommended size: 1200x800 pixels, max 2MB</div>
                                    <input type="hidden" name="current_image" id="edit_current_image" />
                                </div>
                                <div class="col-md-4">
                                    <img id="editImagePreview" src="" class="img-thumbnail mt-2" style="width: 100%; height: 150px; object-fit: cover; border: 1px solid #dee2e6;" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" id="edit_address" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">District</label>
                                    <input type="text" class="form-control" name="district" id="edit_district" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Province</label>
                                    <input type="text" class="form-control" name="province" id="edit_province" required />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Star Rating</label>
                                    <select class="form-select" name="star_rating" id="edit_star_rating" required>
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
                                    <select class="form-select" name="property_type" id="edit_property_type" required>
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
                                    <input type="tel" class="form-control" name="contact_phone" id="edit_contact_phone" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" name="contact_email" id="edit_contact_email" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Website URL</label>
                                    <input type="url" class="form-control" name="website_url" id="edit_website_url" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Total Rooms</label>
                                    <input type="number" class="form-control" name="total_rooms" id="edit_total_rooms" value="0" min="0" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" id="edit_status" required>
                                        <option value="pending">Pending</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="amenities" role="tabpanel">
                        <form id="hotelAmenitiesForm">
                            <input type="hidden" name="hotel_id" id="edit_hotel_id_amenities" />
                            <div class="row">
                                <?php
                                // Fetch all amenities
                                $stmt = $conn->prepare("SELECT * FROM amenities ORDER BY category, amenity_name");
                                $stmt->execute();
                                $amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Group amenities by category
                                $groupedAmenities = [];
                                foreach ($amenities as $amenity) {
                                    $groupedAmenities[$amenity['category']][] = $amenity;
                                }

                                // Define categories
                                $categories = [
                                    'basic' => 'Basic Amenities',
                                    'comfort' => 'Comfort Amenities',
                                    'business' => 'Business Amenities',
                                    'recreation' => 'Recreation Amenities',
                                    'accessibility' => 'Accessibility Features'
                                ];

                                foreach ($categories as $categoryKey => $categoryName):
                                    if (!empty($groupedAmenities[$categoryKey])):
                                ?>
                                        <div class="col-md-6 mb-3">
                                            <h6><?php echo $categoryName; ?></h6>
                                            <div class="list-group">
                                                <?php foreach ($groupedAmenities[$categoryKey] as $amenity): ?>
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-1" type="checkbox"
                                                            name="amenities[]"
                                                            value="<?php echo $amenity['amenity_id']; ?>">
                                                        <i class="fas <?php echo htmlspecialchars($amenity['icon_class']); ?> me-2"></i>
                                                        <?php echo htmlspecialchars($amenity['amenity_name']); ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">Save Amenities</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary" form="editHotelForm">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showBootstrapAlert(type, title, message) {
        let icon = '';
        switch (type) {
            case 'success':
                icon = 'check-circle';
                break;
            case 'error':
                icon = 'exclamation-circle';
                type = 'danger';
                break;
            case 'warning':
                icon = 'exclamation-triangle';
                break;
            case 'info':
                icon = 'info-circle';
                break;
        }

        return `
        <div class='alert alert-${type} alert-dismissible fade show' role='alert'>
            <strong><i class='fas fa-${icon} me-2'></i>${title}</strong>
            ${message ? `<br>${message}` : ''}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>`;
    }

    function editHotel(hotelId) {
        // Fetch hotel details
        fetch(`handlers/get_hotel.php?id=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const hotel = data.data;

                    // Fill the form with hotel data
                    document.getElementById('edit_hotel_id').value = hotel.hotel_id;
                    document.getElementById('edit_hotel_id_amenities').value = hotel.hotel_id;
                    document.getElementById('edit_hotel_name').value = hotel.hotel_name;
                    document.getElementById('edit_description').value = hotel.description;
                    document.getElementById('edit_address').value = hotel.address;
                    document.getElementById('edit_district').value = hotel.district;
                    document.getElementById('edit_province').value = hotel.province;
                    document.getElementById('edit_star_rating').value = hotel.star_rating;
                    document.getElementById('edit_property_type').value = hotel.property_type;
                    document.getElementById('edit_contact_phone').value = hotel.contact_phone;
                    document.getElementById('edit_contact_email').value = hotel.contact_email;
                    document.getElementById('edit_website_url').value = hotel.website_url;
                    document.getElementById('edit_total_rooms').value = hotel.total_rooms;
                    document.getElementById('edit_status').value = hotel.status;

                    // Update image preview
                    const previewImage = document.getElementById('editImagePreview');
                    if (hotel.main_image) {
                        previewImage.src = `../uploads/img/hotels/${hotel.hotel_id}/${hotel.main_image}`;
                    } else {
                        previewImage.src = '../admin/img/placeholder-hotel.jpg';
                    }

                    // Show the modal with footer buttons
                    const editModal = new bootstrap.Modal(document.getElementById('editHotelModal'));
                    editModal.show();
                } else {
                    const alertArea = document.querySelector('.hotels-list');
                    alertArea.insertAdjacentHTML('afterbegin', showBootstrapAlert('error', 'Error', 'Failed to load hotel details'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertArea = document.querySelector('.hotels-list');
                alertArea.insertAdjacentHTML('afterbegin', showBootstrapAlert('error', 'Error', 'Failed to load hotel details'));
            });
    }

    // Handle edit hotel form submission
    document.getElementById('editHotelForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Close the modal
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editHotelModal'));
                    editModal.hide(); // Show success message
                    const alertArea = document.querySelector('.hotels-list');
                    alertArea.insertAdjacentHTML('afterbegin', showBootstrapAlert('success', 'Success', data.message));

                    // Close the modal
                    editModal.hide();

                    // Refresh the hotels list after a short delay to show the alert
                    setTimeout(() => location.reload(), 1500);
                } else {
                    const alertArea = document.querySelector('.hotels-list');
                    alertArea.insertAdjacentHTML('afterbegin',
                        showBootstrapAlert('error', 'Error', data.message || 'Error updating hotel'));
                }
            }).catch(error => {
                console.error('Error:', error);
                const alertArea = document.querySelector('.hotels-list');
                alertArea.insertAdjacentHTML('afterbegin', showBootstrapAlert('error', 'Error', 'Failed to update hotel. Please try again.'));
            });
    });

    // Handle amenities form submission
    document.getElementById('hotelAmenitiesForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const hotelId = document.getElementById('edit_hotel_id_amenities').value;

        fetch('handlers/update_hotel_amenities.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const alertArea = document.querySelector('.hotels-list');
                    alertArea.insertAdjacentHTML('afterbegin',
                        showBootstrapAlert('success', 'Success', 'Hotel amenities updated successfully'));
                } else {
                    const alertArea = document.querySelector('.hotels-list');
                    alertArea.insertAdjacentHTML('afterbegin',
                        showBootstrapAlert('error', 'Error', data.message || 'Error updating hotel amenities'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertArea = document.querySelector('.hotels-list');
                alertArea.insertAdjacentHTML('afterbegin',
                    showBootstrapAlert('error', 'Error', 'Error updating hotel amenities'));
            });
    });
</script>

<?php include_once 'includes/footer.php'; ?>