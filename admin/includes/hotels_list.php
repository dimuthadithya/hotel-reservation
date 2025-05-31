<?php
$sql = "SELECT * FROM hotels ORDER BY created_at DESC";
$stmt = $conn->query($sql);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th width="50">#</th>
                <th>Name</th>
                <th>Type</th>
                <th>District</th>
                <th>Province</th>
                <th width="70">Star</th>
                <th width="100">Status</th>
                <th>Created</th>
                <th width="150">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($hotels) === 0): ?>
                <tr>
                    <td colspan="9" class="text-center">No hotels found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($hotels as $i => $hotel): ?> <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img
                                    src="<?= !empty($hotel['main_image']) ? '../uploads/img/hotels/' . $hotel['hotel_id'] . '/' . $hotel['main_image'] : '../admin/img/placeholder-hotel.jpg' ?>"
                                    alt="<?= htmlspecialchars($hotel['hotel_name'] ?? 'Hotel Image') ?>"
                                    class="hotel-thumb me-2"
                                    onerror="this.onerror=null; this.src='../admin/img/placeholder-hotel.jpg';"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                                <?= htmlspecialchars($hotel['hotel_name']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars(ucfirst($hotel['property_type'])) ?></td>
                        <td><?= htmlspecialchars($hotel['district']) ?></td>
                        <td><?= htmlspecialchars($hotel['province']) ?></td>
                        <td><?= htmlspecialchars($hotel['star_rating']) ?>★</td>
                        <td><span class="badge bg-<?= $hotel['status'] === 'active' ? 'success' : ($hotel['status'] === 'pending' ? 'warning' : 'secondary') ?>"><?= ucfirst($hotel['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($hotel['created_at'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="../hotel-details.php?id=<?= $hotel['hotel_id'] ?>" target="_blank" class="text-bold">
                                    <button class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye fa-sm "></i>
                                    </button>
                                </a>
                                <button onclick="editHotel(<?= $hotel['hotel_id'] ?>)" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit fa-sm"></i>
                                </button> <a href="manage_hotel_rooms.php?hotel_id=<?= $hotel['hotel_id'] ?>" class="btn btn-success btn-sm" title="Manage Rooms">
                                    <i class="fas fa-bed fa-sm"></i>
                                </a>
                                <a href="room_types.php?hotel_id=<?= $hotel['hotel_id'] ?>" class="btn btn-primary btn-sm" title="Room Types">
                                    <i class="fas fa-list fa-sm"></i>
                                </a>
                                <form action="handlers/delete_hotel.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="hotel_id" value="<?= $hotel['hotel_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<style>
    .action-buttons {
        display: flex;
        gap: 4px;
    }

    .action-buttons .btn,
    .action-buttons form .btn {
        padding: 0.25rem;
        font-size: 0.8rem;
        line-height: 1;
        border-radius: 0.2rem;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons .fas {
        font-size: 0.75rem;
    }

    .table> :not(caption)>*>* {
        padding: 0.4rem 0.5rem;
        vertical-align: middle;
    }

    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
    }

    .hotel-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
</style>