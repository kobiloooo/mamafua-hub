<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class User
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (:name, :email, :phone, :password_hash, :role, :status)');
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':role' => $data['role'],
            ':status' => 'active',
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, name, email, phone, role, status, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function allMamafuasWithStatus(): array
    {
        $sql = 'SELECT u.id AS user_id, m.id AS mamafua_id, u.name, u.email, u.phone, u.status, m.verification_status, m.skills, m.location
                FROM users u
                LEFT JOIN mamafuas m ON m.user_id = u.id
                WHERE u.role = "mamafua"
                ORDER BY u.created_at DESC';
        return Database::connection()->query($sql)->fetchAll();
    }
}
