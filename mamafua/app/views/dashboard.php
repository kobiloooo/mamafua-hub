<section>
    <h1 class="h4 mb-3">My Dashboard</h1>
    <?php if (empty($user)): ?>
        <div class="alert alert-warning">Please login to view dashboard.</div>
    <?php else: ?>
        <?php if ($user['role'] === 'mamafua'): ?>
            <div class="card p-3 mb-3 shadow-sm">
                <h2 class="h6">Upload Verification Documents</h2>
                <form id="documentForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div class="row g-2">
                        <div class="col-md-4"><select class="form-select" name="document_type"><option value="id_document">National ID</option><option value="photo">Profile Photo</option><option value="police_clearance">Police Clearance</option></select></div>
                        <div class="col-md-5"><input class="form-control" type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" required></div>
                        <div class="col-md-3"><button class="btn btn-brand w-100">Upload</button></div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <div id="bookingHistory" class="card p-3 shadow-sm">Loading your bookings...</div>
    <?php endif; ?>
</section>
