      <?php
        /**
         * This component expects the following data in the $hotel array:
         * - hotel_id: The ID of the hotel
         * - hotel_name: The name of the hotel
         * - main_image: The hotel's main image URL from hotel_images table
         * - star_rating: Number of stars (1-5)
         * - address: Hotel address
         * - base_price: Price per night (from room_types table, using minimum price)
         * - average_rating: Rating from hotels table
         * - total_reviews: Total reviews from hotels table
         * - amenities: Array of amenities from hotel_amenities junction table
         * - property_type: Type of property (hotel, resort, villa, etc.)
         */

        require_once __DIR__ . '/../includes/utility_functions.php';
        ?> <div class="hotel-card mb-4"
          data-property-type="<?= strtolower($hotel['property_type']) ?>"
          data-star-rating="<?= intval($hotel['star_rating']) ?>"
          data-rating="<?= floatval($hotel['average_rating']) ?>"
          data-price="<?= floatval($hotel['base_price']) ?>"
          data-amenities='<?= htmlspecialchars(json_encode($amenities), ENT_QUOTES, 'UTF-8') ?>'>
          <div class="row g-0">
              <div class="col-md-4">
                  <div class="hotel-image">
                      <img
                          src="<?= $hotel['main_image'] ? 'uploads/img/hotels/' . $hotel['hotel_id'] . '/' . $hotel['main_image'] : 'assets/img/hotel-placeholder.jpg' ?>"
                          alt="<?= htmlspecialchars($hotel['hotel_name']) ?>"
                          class="img-fluid"
                          onerror="this.src='assets/img/hotel-placeholder.jpg';" />
                  </div>
              </div>
              <div class="col-md-8">
                  <div class="card-body">
                      <div class="d-flex justify-content-between align-items-start">
                          <div>
                              <h5 class="card-title"><?= htmlspecialchars($hotel['hotel_name']) ?></h5>
                              <div class="hotel-rating">
                                  <span class="stars"><?= str_repeat('★', intval($hotel['star_rating'])) ?></span>
                                  <span class="rating-text"><?= intval($hotel['star_rating']) ?>-star hotel</span>
                              </div>
                              <p class="location">
                                  <i class="fas fa-map-marker-alt"></i>
                                  <?= htmlspecialchars($hotel['address']) ?>
                                  <?php if ($hotel['district']): ?>
                                      <br><small class="text-muted"><?= htmlspecialchars($hotel['district']) ?>, <?= htmlspecialchars($hotel['province']) ?></small>
                                  <?php endif; ?>
                              </p>
                          </div>
                          <div class="text-end">
                              <div class="review-score"> <?php if ($hotel['average_rating'] > 0): ?> <span class="score"><?= number_format($hotel['average_rating'], 1) ?></span>
                                      <span class="score-text"><?= getRatingText($hotel['average_rating']) ?></span>
                                  <?php endif; ?>
                                  <span class="review-count"><?= $hotel['total_reviews'] ?? 0 ?> reviews</span>
                              </div>
                          </div>
                      </div>
                      <div class="amenities">
                          <?php if (!empty($amenities)):
                                foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                                  <span class="badge bg-light text-dark me-2"><?= htmlspecialchars($amenity) ?></span>
                          <?php endforeach;
                            endif; ?>
                      </div>
                      <div class="mt-3 d-flex justify-content-between align-items-end">
                          <div>
                              <small class="text-success">Free cancellation available</small>
                          </div>
                          <div class="text-end">
                              <div class="price">
                                  <small class="text-muted">per night</small>
                                  <h5 class="mb-0">
                                      <?php if (isset($hotel['base_price']) && $hotel['base_price'] !== null): ?>
                                          LKR <?= number_format($hotel['base_price']) ?>
                                      <?php else: ?>
                                          <span class="text-muted">Price on request</span>
                                      <?php endif; ?>
                                  </h5>
                              </div>
                              <a href="hotel-details.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-primary mt-2">View Details</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>