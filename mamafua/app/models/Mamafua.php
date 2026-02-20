<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Mamafua
{
    public static function createProfile(int $userId, string $skills, string $location, string $availability): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO mamafuas (user_id, skills, location, availability, verification_status) VALUES (:user_id, :skills, :location, :availability, :verification_status)');
        $stmt->execute([
            ':user_id' => $userId,
            ':skills' => $skills,
            ':location' => $location,
            ':availability' => $availability,
            ':verification_status' => 'pending',
        ]);
    }

    public static function upsertDocument(int $mamafuaId, string $type, string $path): void
    {
        $sql = 'INSERT INTO mamafua_documents (mamafua_id, document_type, file_path, status)
                VALUES (:mamafua_id, :document_type, :file_path, "submitted")
                ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), status = "submitted", updated_at = CURRENT_TIMESTAMP';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':mamafua_id' => $mamafuaId,
            ':document_type' => $type,
            ':file_path' => $path,
        ]);
    }

    public static function byUserId(int $userId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM mamafuas WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public static function verifiedList(): array
    {
        $sql = 'SELECT m.id, u.name, u.phone, m.skills, m.location, m.availability, m.rating_average
                FROM mamafuas m
                INNER JOIN users u ON u.id = m.user_id
                WHERE m.verification_status = "verified" AND u.status = "active"
                ORDER BY m.rating_average DESC, m.completed_jobs DESC';
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function setVerification(int $mamafuaId, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE mamafuas SET verification_status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $mamafuaId]);
    }
}
