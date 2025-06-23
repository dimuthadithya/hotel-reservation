<?php
include_once 'includes/header.php';
include_once 'includes/sidebar.php';
include_once '../config/db.php';
?>

<style>
    .action-buttons .btn-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1;
    }

    .action-buttons .fas {
        font-size: 0.75rem;
    }
</style>

<!-- Main Content -->
<div class="admin-main">
    <?php include 'includes/alert_message.php'; ?>
    <div class="content-header">
        <h2>Booking Management</h2>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Booking Ref</th>
                            <th>Hotel</th>
                            <th>Guest</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Room</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, h.hotel_name, rt.type_name as room_type, r.room_number 
                                FROM bookings b 
                                JOIN hotels h ON b.hotel_id = h.hotel_id 
                                JOIN room_types rt ON b.room_type_id = rt.room_type_id
                                JOIN room_bookings rb ON b.booking_id = rb.booking_id
                                JOIN rooms r ON rb.room_id = r.room_id
                                ORDER BY b.created_at DESC";
                        $stmt = $conn->query($sql);
                        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($bookings as $booking):
                            // Determine status class
                            $statusClass = match ($booking['booking_status']) {
                                'confirmed' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                'checked_in' => 'info',
                                'checked_out' => 'secondary',
                                default => 'secondary'
                            };
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['booking_reference']) ?></td>
                                <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
                                <td>
                                    <?= htmlspecialchars($booking['guest_name']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($booking['guest_email']) ?></small>
                                </td>
                                <td><?= date('M d, Y', strtotime($booking['check_in_date'])) ?></td>
                                <td><?= date('M d, Y', strtotime($booking['check_out_date'])) ?></td>
                                <td>
                                    Room <?= htmlspecialchars($booking['room_number']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($booking['room_type']) ?></small>
                                </td>
                                <td>LKR <?= number_format($booking['total_amount'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= ucfirst($booking['booking_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="viewBooking(<?= $booking['booking_id'] ?>)" class="btn btn-info btn-sm" title="View Details">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </button>
                                        <?php if ($booking['booking_status'] === 'pending'): ?>
                                            <form method="POST" action="handlers/update_booking_status.php" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-success btn-sm" title="Confirm" onclick="return confirm('Are you sure you want to confirm this booking?')">
                                                    <i class="fas fa-check fa-sm"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($booking['booking_status'] === 'confirmed'): ?>
                                            <form method="POST" action="handlers/update_booking_status.php" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <input type="hidden" name="status" value="checked_in">
                                                <button type="submit" class="btn btn-primary btn-sm" title="Check In" onclick="return confirm('Are you sure you want to check in this booking?')">
                                                    <i class="fas fa-sign-in-alt fa-sm"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($booking['booking_status'] === 'checked_in'): ?>
                                            <form method="POST" action="handlers/update_booking_status.php" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <input type="hidden" name="status" value="checked_out">
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Check Out" onclick="return confirm('Are you sure you want to check out this booking?')">
                                                    <i class="fas fa-sign-out-alt fa-sm"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if (in_array($booking['booking_status'], ['pending', 'confirmed'])): ?>
                                            <form method="POST" action="handlers/update_booking_status.php" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Cancel" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    <i class="fas fa-times fa-sm"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Booking Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Custom JS -->
<script src="js/bookings.js"></script>

<?php include_once 'includes/footer.php'; ?>