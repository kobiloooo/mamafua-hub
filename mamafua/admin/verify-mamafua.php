<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Mamafua.php';
require_once __DIR__ . '/../app/models/AdminLog.php';

$user = requireAuth(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    $mamafuaId = (int) ($_POST['mamafua_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'pending');
    if (in_array($status, ['verified', 'suspended', 'rejected', 'pending'], true)) {
        Mamafua::setVerification($mamafuaId, $status);
        AdminLog::add($user['id'], 'verification_update', 'mamafua', $mamafuaId, 'Status changed to ' . $status);
    }
}

$mamafuas = User::allMamafuasWithStatus();
$csrfToken = csrfToken();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><title>Verify Mamafua</title></head>
<body><div class="container py-4"><h1 class="h4">Mamafua Verification</h1>
<table class="table table-bordered table-striped"><thead><tr><th>Name</th><th>Phone</th><th>Skills</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($mamafuas as $m): ?><tr><td><?= htmlspecialchars($m['name']) ?></td><td><?= htmlspecialchars($m['phone']) ?></td><td><?= htmlspecialchars($m['skills'] ?? '') ?></td><td><?= htmlspecialchars($m['verification_status'] ?? 'pending') ?></td><td>
<form method="post" class="d-flex gap-2"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="mamafua_id" value="<?= (int)$m['mamafua_id'] ?>"><select class="form-select form-select-sm" name="status"><option>pending</option><option>verified</option><option>suspended</option><option>rejected</option></select><button class="btn btn-sm btn-primary">Save</button></form>
</td></tr><?php endforeach; ?>
</tbody></table></div></body></html>
