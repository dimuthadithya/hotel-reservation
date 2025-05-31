      <?php
        // filepath: c:\laragon\www\hotel-reservation\components\hotelCard.php

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
         * - amenities: JSON string of amenities from hotel_amenities junction table
         */

        require_once __DIR__ . '/../includes/utility_functions.php';
        ?>
      <div class="hotel-card mb-4">
          <div class="row g-0">
              <div class="col-md-4">                  <div class="hotel-image">
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
                                  <span class="review-count"><?= $hotel['total_reviews'] ?? rand(10, 50) ?> reviews</span>
                              </div>
                          </div>
                      </div>
                      <div class="amenities">
                          <?php
                            $amenities = json_decode($hotel['amenities'], true);
                            if (is_array($amenities)) {
                                foreach (array_slice($amenities, 0, 4) as $amenity): ?>
                                  <span class="badge bg-light text-dark me-2"><?= htmlspecialchars($amenity) ?></span>
                          <?php endforeach;
                            }
                            ?>
                      </div>
                      <div class="mt-3 d-flex justify-content-between align-items-end">
                          <div>
                              <small class="text-success">Free cancellation available</small>
                          </div>
                          <div class="text-end">
                              <div class="price">
                                  <small class="text-muted">per night</small> <?php
                                                                                // Get minimum price from room types
                                                                                $sql = "SELECT MIN(base_price) as min_price FROM room_types WHERE hotel_id = :hotel_id AND status = 'active'";
                                                                                $stmt = $conn->prepare($sql);
                                                                                $stmt->execute(['hotel_id' => $hotel['hotel_id']]);
                                                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                                $minPrice = $result['min_price'] ?? 0;
                                                                                ?>
                                  <h5 class="mb-0">LKR <?= number_format($minPrice) ?></h5>
                              </div>
                              <a href="hotel-details.php?id=<?= $hotel['hotel_id'] ?>" class="btn btn-primary mt-2">View Details</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>