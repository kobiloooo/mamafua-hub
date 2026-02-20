<?php

declare(strict_types=1);

require_once __DIR__ . '/../helpers/common.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Mamafua.php';

class AuthController
{
    public static function register(): void
    {
        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 422);
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = sanitize($_POST['phone'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $role = sanitize($_POST['role'] ?? 'client');

        if (!$email || strlen($password) < 8 || !in_array($role, ['client', 'mamafua'], true)) {
            jsonResponse(['success' => false, 'message' => 'Validation failed.'], 422);
        }

        if (User::findByEmail($email)) {
            jsonResponse(['success' => false, 'message' => 'Email already exists.'], 409);
        }

        $userId = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => $role,
        ]);

        if ($role === 'mamafua') {
            $skills = implode(',', $_POST['skills'] ?? []);
            Mamafua::createProfile($userId, $skills, sanitize($_POST['location'] ?? 'Nairobi'), sanitize($_POST['availability'] ?? 'Weekdays'));
        }

        jsonResponse(['success' => true, 'message' => 'Registration successful.']);
    }

    public static function login(): void
    {
        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 422);
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string) ($_POST['password'] ?? '');
        if (!$email) {
            jsonResponse(['success' => false, 'message' => 'Invalid credentials.'], 422);
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        if ($user['status'] !== 'active') {
            jsonResponse(['success' => false, 'message' => 'Account is suspended.'], 403);
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        jsonResponse(['success' => true, 'message' => 'Login successful.', 'role' => $user['role']]);
    }

    public static function logout(): void
    {
        session_destroy();
        jsonResponse(['success' => true, 'message' => 'Logged out.']);
    }
}
