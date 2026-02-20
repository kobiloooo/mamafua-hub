<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Booking
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $sql = 'INSERT INTO bookings (client_id, mamafua_id, service_type, pricing_plan, booking_date, start_time, end_time, location, amount, status, payment_status)
                VALUES (:client_id, :mamafua_id, :service_type, :pricing_plan, :booking_date, :start_time, :end_time, :location, :amount, "pending_payment", "pending")';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function slotAvailable(int $mamafuaId, string $date, string $startTime, string $endTime): bool
    {
        $sql = 'SELECT COUNT(*) FROM bookings
                WHERE mamafua_id = :mamafua_id
                AND booking_date = :booking_date
                AND status IN ("pending_payment", "confirmed", "in_progress")
                AND (
                    (start_time <= :start_time AND end_time > :start_time) OR
                    (start_time < :end_time AND end_time >= :end_time) OR
                    (start_time >= :start_time AND end_time <= :end_time)
                )';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':mamafua_id' => $mamafuaId,
            ':booking_date' => $date,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        ]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public static function updatePaymentStatus(int $bookingId, string $paymentStatus, string $bookingStatus): void
    {
        $stmt = Database::connection()->prepare('UPDATE bookings SET payment_status = :payment_status, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute([':payment_status' => $paymentStatus, ':status' => $bookingStatus, ':id' => $bookingId]);
    }

    public static function byUser(int $userId, string $role): array
    {
        $field = $role === 'client' ? 'client_id' : 'u.id';
        $sql = 'SELECT b.*, c.name AS client_name, muser.name AS mamafua_name
                FROM bookings b
                INNER JOIN users c ON c.id = b.client_id
                INNER JOIN mamafuas m ON m.id = b.mamafua_id
                INNER JOIN users muser ON muser.id = m.user_id
                WHERE ' . ($role === 'client' ? 'b.client_id = :uid' : 'm.user_id = :uid') . '
                ORDER BY b.booking_date DESC, b.start_time DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $sql = 'SELECT b.*, c.name AS client_name, muser.name AS mamafua_name
                FROM bookings b
                INNER JOIN users c ON c.id = b.client_id
                INNER JOIN mamafuas m ON m.id = b.mamafua_id
                INNER JOIN users muser ON muser.id = m.user_id
                ORDER BY b.created_at DESC';
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function updateStatus(int $bookingId, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE bookings SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $bookingId]);
    }
}
