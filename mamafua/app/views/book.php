<section>
    <h1 class="h4 mb-3">Book in 3 easy steps</h1>
    <ol class="small text-muted"><li>Choose a verified mamafua</li><li>Pick date & service</li><li>Pay via M-Pesa STK Push</li></ol>
    <?php if (empty($user) || $user['role'] !== 'client'): ?>
        <div class="alert alert-warning">Please login as a client to book services.</div>
    <?php else: ?>
        <form id="bookingForm" class="card p-3 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="row g-3">
                <div class="col-md-6"><label>Mamafua</label><select class="form-select" name="mamafua_id" required><?php foreach ($mamafuas as $m): ?><option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['name']) ?> - <?= htmlspecialchars($m['location']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label>Service</label><select class="form-select" name="service_type"><option>cleaning</option><option>laundry</option><option>childcare</option><option>cooking</option></select></div>
                <div class="col-md-4"><label>Pricing Plan</label><select class="form-select" name="pricing_plan" id="pricingPlan"><option value="hourly" data-amount="600">Hourly (KES 600)</option><option value="daily" data-amount="2500">Daily (KES 2,500)</option><option value="weekly" data-amount="12000">Weekly (KES 12,000)</option></select></div>
                <div class="col-md-4"><label>Date</label><input class="form-control" type="date" name="booking_date" required></div>
                <div class="col-md-2"><label>Start</label><input class="form-control" type="time" name="start_time" required></div>
                <div class="col-md-2"><label>End</label><input class="form-control" type="time" name="end_time" required></div>
                <div class="col-md-6"><label>Location</label><input class="form-control" name="location" required></div>
                <div class="col-md-6"><label>M-Pesa Phone</label><input class="form-control" name="phone_number" pattern="2547[0-9]{8}" required></div>
                <div class="col-md-3"><label>Amount</label><input class="form-control" name="amount" id="amountInput" value="600" readonly></div>
            </div>
            <button class="btn btn-brand mt-3" type="submit">Confirm & Pay</button>
        </form>
    <?php endif; ?>
</section>
