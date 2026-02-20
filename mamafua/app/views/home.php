<section class="hero p-4 p-md-5 rounded-4 text-white mb-4">
    <h1 class="display-6 fw-bold">Trusted mamafuas for your home, on your schedule.</h1>
    <p class="lead">Book verified housekeepers for cleaning, laundry, childcare, and cooking in under 3 steps.</p>
    <a href="index.php?page=book" class="btn btn-warning btn-lg">Book a Mamafua</a>
</section>

<section class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card h-100 p-3 text-center"><h5>🧹 Cleaning</h5><p class="small">Deep and daily cleaning plans.</p></div></div>
    <div class="col-6 col-md-3"><div class="card h-100 p-3 text-center"><h5>👕 Laundry</h5><p class="small">Washing, drying, ironing support.</p></div></div>
    <div class="col-6 col-md-3"><div class="card h-100 p-3 text-center"><h5>👶 Childcare</h5><p class="small">Experienced child-friendly carers.</p></div></div>
    <div class="col-6 col-md-3"><div class="card h-100 p-3 text-center"><h5>🍲 Cooking</h5><p class="small">Home meals and meal prep.</p></div></div>
</section>

<section>
    <h2 class="h4 mb-3">Verified Mamafuas near you</h2>
    <div class="row g-3">
        <?php foreach ($mamafuas as $m): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($m['name']) ?></h5>
                        <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($m['location']) ?></p>
                        <p class="mb-1"><strong>Skills:</strong> <?= htmlspecialchars($m['skills']) ?></p>
                        <p class="mb-2"><strong>Rating:</strong> ⭐ <?= number_format((float)$m['rating_average'], 1) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
