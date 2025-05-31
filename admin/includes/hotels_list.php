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
                <?php foreach ($hotels as $i => $hotel): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($hotel['hotel_name']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($hotel['property_type'])) ?></td>
                        <td><?= htmlspecialchars($hotel['district']) ?></td>
                        <td><?= htmlspecialchars($hotel['province']) ?></td>
                        <td><?= htmlspecialchars($hotel['star_rating']) ?>★</td>
                        <td><span class="badge bg-<?= $hotel['status'] === 'active' ? 'success' : ($hotel['status'] === 'pending' ? 'warning' : 'secondary') ?>"><?= ucfirst($hotel['status']) ?></span></td>
                        <td><?= date('Y-m-d', strtotime($hotel['created_at'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="viewHotel(<?= $hotel['hotel_id'] ?>)" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye fa-sm"></i>
                                </button>
                                <button onclick="editHotel(<?= $hotel['hotel_id'] ?>)" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit fa-sm"></i>
                                </button>
                                <form action="handlers/delete_hotel.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="hotel_id" value="<?= $hotel['hotel_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
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
</style>