<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName ?? 'Mamafua') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-brand sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Mamafua 🇰🇪</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php?page=home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=book">Book</a></li>
                <?php if (!empty($user)): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=dashboard">Dashboard</a></li>
                <?php endif; ?>
            </ul>
            <div class="ms-lg-3 mt-3 mt-lg-0">
                <?php if (empty($user)): ?>
                    <a href="index.php?page=login" class="btn btn-light btn-sm">Login</a>
                    <a href="index.php?page=register" class="btn btn-warning btn-sm">Join Mamafua</a>
                <?php else: ?>
                    <button class="btn btn-light btn-sm" onclick="logout()">Logout</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container py-4">
