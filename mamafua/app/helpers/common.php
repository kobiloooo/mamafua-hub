<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name']);
    session_start();
}

function appConfig(): array
{
    static $config;
    if (!$config) {
        $config = require __DIR__ . '/../config/config.php';
    }
    return $config;
}

function csrfToken(): string
{
    $key = appConfig()['csrf_key'];
    if (empty($_SESSION[$key])) {
        $_SESSION[$key] = bin2hex(random_bytes(32));
    }
    return $_SESSION[$key];
}

function verifyCsrf(?string $token): bool
{
    $key = appConfig()['csrf_key'];
    return isset($_SESSION[$key]) && hash_equals($_SESSION[$key], (string) $token);
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireAuth(array $roles = []): array
{
    $user = currentUser();
    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'Unauthorised.'], 401);
    }
    if ($roles && !in_array($user['role'], $roles, true)) {
        jsonResponse(['success' => false, 'message' => 'Forbidden.'], 403);
    }
    return $user;
}

function render(string $view, array $data = []): void
{
    extract($data);
    include __DIR__ . '/../views/layout-header.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/layout-footer.php';
}
