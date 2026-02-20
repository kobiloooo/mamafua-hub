<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/models/Payment.php';
require_once __DIR__ . '/../app/models/User.php';

$user = requireAuth(['admin']);
$bookings = Booking::all();
$payments = Payment::all();
$mamafuas = User::allMamafuasWithStatus();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><title>Admin Dashboard</title></head>
<body class="bg-light"><div class="container py-4"><h1 class="h3">Admin Dashboard</h1><p>Welcome, <?= htmlspecialchars($user['name']) ?>.</p>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card p-3"><h2 class="h6">Total Bookings</h2><strong><?= count($bookings) ?></strong></div></div><div class="col-md-4"><div class="card p-3"><h2 class="h6">Payments</h2><strong><?= count($payments) ?></strong></div></div><div class="col-md-4"><div class="card p-3"><h2 class="h6">Mamafuas</h2><strong><?= count($mamafuas) ?></strong></div></div></div>
<a class="btn btn-primary btn-sm" href="verify-mamafua.php">Verify Mamafuas</a> <a class="btn btn-outline-primary btn-sm" href="bookings.php">Manage Bookings</a>
</div></body></html>
