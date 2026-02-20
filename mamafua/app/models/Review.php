<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Review
{
    public static function create(array $data): void
    {
        $sql = 'INSERT INTO reviews (booking_id, client_id, mamafua_id, rating, comment, moderation_status)
                VALUES (:booking_id, :client_id, :mamafua_id, :rating, :comment, "pending")';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($data);
    }
}
