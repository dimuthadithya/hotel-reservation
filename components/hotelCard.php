<?php

/**
 * Hotel card component with vertical layout:
 * - Image on top
 * - Text and info below
 */
require_once __DIR__ . '/../includes/utility_functions.php';
?>
<div class="hotel-card card mb-4"
    data-property-type="<?= strtolower($hotel['property_type']) ?>"
    data-star-rating="<?= intval($hotel['star_rating']) ?>"
    data-rating="<?= floatval($hotel['average_rating']) ?>"
    data-price="<?= floatval($hotel['base_price']) ?>"
    data-amenities='<?= htmlspecialchars(json_encode($amenities), ENT_QUOTES, 'UTF-8') ?>'>

    <!-- Hotel Image -->
    <img
        src="<?= $hotel['main_image'] ? 'uploads/img/hotels/' . $hotel['hotel_id'] . '/' . $hotel['main_image'] : 'assets/img/hotel-placeholder.jpg' ?>"
        alt="<?= htmlspecialchars($hotel['hotel_name']) ?>"
        class="card-img-top"
        onerror="this.src='assets/img/hotel-placeholder.jpg';" />

    <!-- Hotel Info -->
    <div class="card-body d-flex flex-column">
        <div>
            <h5 class="card-title"><?= htmlspecialchars($hotel['hotel_name']) ?></h5>
            <div class="hotel-rating mb-2">
                <span class="stars"><?= str_repeat('★', intval($hotel['star_rating'])) ?></span>
                <span class="rating-text"><?= intval($hotel['star_rating']) ?>-star hotel</span>
            </div>
            <p class="location mb-2">
                <i class="fas fa-map-marker-alt me-1"></i>
                <?= htmlspecialchars($hotel['address']) ?><br>
                <?php if ($hotel['district']): ?>
                    <small class="text-muted"><?= htmlspecialchars($hotel['district']) ?>, <?= htmlspecialchars($hotel['province']) ?></small>
                <?php endif; ?>
            </p>
        </div>

        <!-- Review Section -->
        <?php if ($hotel['average_rating'] > 0): ?>
            <div class="review-score mb-2">
                <span class="score"><?= number_format($hotel['average_rating'], 1) ?></span>
                <span class="score-text"><?= getRatingText($hotel['average_rating']) ?></span>
                <span class="review-count">(<?= $hotel['total_reviews'] ?? 0 ?> reviews)</span>
            </div>
        <?php endif; ?>

        <!-- Amenities -->
        <div class="amenities mb-2">
            <?php if (!empty($amenities)):
                foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                    <span class="badge bg-light text-dark me-1 mb-1"><?= htmlspecialchars($amenity) ?></span>
            <?php endforeach;
            endif; ?>
        </div>

        <!-- Price & CTA -->
        <div class="mt-3 ctz-card-2 ">
            <div class="text-start ctz-card">
                <small class="text-muted">per night</small>
                <h5 class="mb-0">
                    <?php if (isset($hotel['base_price']) && $hotel['base_price'] !== null): ?>
                        LKR <?= number_format($hotel['base_price']) ?>
                    <?php else: ?>
                        <span class="text-muted">Price on request</span>
                    <?php endif; ?>
                </h5>
                <a href="hotel-details.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-primary btn-sm mt-2">View Details</a>
            </div>
        </div>
    </div>
</div>