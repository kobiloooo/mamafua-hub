<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/Mamafua.php';

$page = $_GET['page'] ?? 'home';
$user = currentUser();

$routes = [
    'home' => 'home',
    'login' => 'login',
    'register' => 'register',
    'book' => 'book',
    'dashboard' => 'dashboard',
];

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'home';
}

$mamafuas = [];
try {
    $mamafuas = Mamafua::verifiedList();
} catch (Throwable $e) {
    $mamafuas = [];
}

$data = [
    'user' => $user,
    'mamafuas' => $mamafuas,
    'csrfToken' => csrfToken(),
    'appName' => appConfig()['app']['name'],
];

render($routes[$page], $data);
