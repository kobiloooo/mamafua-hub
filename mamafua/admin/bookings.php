<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/models/AdminLog.php';

$user = requireAuth(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'confirmed');
    Booking::updateStatus($bookingId, $status);
    AdminLog::add($user['id'], 'booking_status_update', 'booking', $bookingId, 'Status changed to ' . $status);
}

$bookings = Booking::all();
$csrfToken = csrfToken();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><title>Bookings</title></head>
<body><div class="container py-4"><h1 class="h4">All Bookings & Disputes</h1>
<table class="table table-hover"><thead><tr><th>#</th><th>Client</th><th>Mamafua</th><th>Service</th><th>Date</th><th>Status</th><th>Payment</th><th>Update</th></tr></thead><tbody>
<?php foreach ($bookings as $b): ?><tr><td><?= (int)$b['id'] ?></td><td><?= htmlspecialchars($b['client_name']) ?></td><td><?= htmlspecialchars($b['mamafua_name']) ?></td><td><?= htmlspecialchars($b['service_type']) ?></td><td><?= htmlspecialchars($b['booking_date']) ?></td><td><?= htmlspecialchars($b['status']) ?></td><td><?= htmlspecialchars($b['payment_status']) ?></td><td>
<form method="post" class="d-flex gap-2"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><select name="status" class="form-select form-select-sm"><option>confirmed</option><option>in_progress</option><option>completed</option><option>cancelled</option><option>disputed</option></select><button class="btn btn-sm btn-primary">Apply</button></form>
</td></tr><?php endforeach; ?>
</tbody></table></div></body></html>
