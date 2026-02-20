<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class AdminLog
{
    public static function add(int $adminId, string $action, string $targetType, int $targetId, string $notes): void
    {
        $sql = 'INSERT INTO admin_logs (admin_id, action, target_type, target_id, notes) VALUES (:admin_id, :action, :target_type, :target_id, :notes)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':admin_id' => $adminId,
            ':action' => $action,
            ':target_type' => $targetType,
            ':target_id' => $targetId,
            ':notes' => $notes,
        ]);
    }
}
