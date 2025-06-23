<div class="col-md-4">
    <div class="card room-card">
        <img
            src="./uploads/img/hotels/<?php echo $hotelId . '/' . $hotelImage; ?>"
            class="card-img-top"
            alt="Luxury City View Suite" />
        <div class="card-body">
            <h5 class="card-title"><?php echo $hotelName; ?></h5>
            <p class="card-text">
                <?php echo $hotelDistrict; ?>
            </p>
            <a href="./hotel-details.php?id=<?php echo $hotelId; ?>" class="btn btn-primary">Learn More</a>
        </div>
    </div>
</div>