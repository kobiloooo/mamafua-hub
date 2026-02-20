<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';
loadEnv(dirname(__DIR__, 2) . '/.env');

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Mamafua',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost/mamafua/public',
    ],
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'name' => $_ENV['DB_NAME'] ?? 'mamafua',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
    ],
    'session_name' => $_ENV['SESSION_NAME'] ?? 'MAMAFUA_SESSION',
    'csrf_key' => $_ENV['CSRF_KEY'] ?? 'MAMAFUA_CSRF',
    'mpesa' => [
        'env' => $_ENV['MPESA_ENV'] ?? 'sandbox',
        'consumer_key' => $_ENV['MPESA_CONSUMER_KEY'] ?? '',
        'consumer_secret' => $_ENV['MPESA_CONSUMER_SECRET'] ?? '',
        'shortcode' => $_ENV['MPESA_SHORTCODE'] ?? '',
        'passkey' => $_ENV['MPESA_PASSKEY'] ?? '',
        'callback_url' => $_ENV['MPESA_CALLBACK_URL'] ?? '',
        'timeout_url' => $_ENV['MPESA_TIMEOUT_URL'] ?? '',
        'stk_url' => $_ENV['MPESA_STK_URL'] ?? '',
        'token_url' => $_ENV['MPESA_TOKEN_URL'] ?? '',
    ],
];
