<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

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
    <div class="amenities-list">
        <div class="row g-4">
            <div class="col-md-4">
                <h5>Basic Amenities</h5>
                <div class="list-group" id="basicAmenities">
                    <!-- Basic amenities will be loaded here -->
                </div>
            </div>
            <div class="col-md-4">
                <h5>Comfort Amenities</h5>
                <div class="list-group" id="comfortAmenities">
                    <!-- Comfort amenities will be loaded here -->
                </div>
            </div>
            <div class="col-md-4">
                <h5>Recreation Amenities</h5>
                <div class="list-group" id="recreationAmenities">
                    <!-- Recreation amenities will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>