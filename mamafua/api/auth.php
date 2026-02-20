<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/controllers/AuthController.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        AuthController::register();
        break;
    case 'login':
        AuthController::login();
        break;
    case 'logout':
        AuthController::logout();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Unknown action.'], 404);
}
