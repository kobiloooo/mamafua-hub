<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Payment
{
    public static function createPending(array $data): int
    {
        $pdo = Database::connection();
        $sql = 'INSERT INTO payments (booking_id, phone_number, amount, checkout_request_id, merchant_request_id, mpesa_receipt_number, status, raw_response)
                VALUES (:booking_id, :phone_number, :amount, :checkout_request_id, :merchant_request_id, NULL, "pending", :raw_response)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function markPaid(string $checkoutRequestId, string $receipt): ?array
    {
        $stmt = Database::connection()->prepare('UPDATE payments SET status = "paid", mpesa_receipt_number = :receipt, updated_at = CURRENT_TIMESTAMP WHERE checkout_request_id = :checkout');
        $stmt->execute([':receipt' => $receipt, ':checkout' => $checkoutRequestId]);
        return self::findByCheckout($checkoutRequestId);
    }

    public static function markFailed(string $checkoutRequestId, array $payload): ?array
    {
        $stmt = Database::connection()->prepare('UPDATE payments SET status = "failed", raw_response = :raw_response, updated_at = CURRENT_TIMESTAMP WHERE checkout_request_id = :checkout');
        $stmt->execute([':raw_response' => json_encode($payload), ':checkout' => $checkoutRequestId]);
        return self::findByCheckout($checkoutRequestId);
    }

    public static function findByCheckout(string $checkoutRequestId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM payments WHERE checkout_request_id = :checkout LIMIT 1');
        $stmt->execute([':checkout' => $checkoutRequestId]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        $sql = 'SELECT p.*, b.service_type, b.booking_date, u.name AS client_name
                FROM payments p
                INNER JOIN bookings b ON b.id = p.booking_id
                INNER JOIN users u ON u.id = b.client_id
                ORDER BY p.created_at DESC';
        return Database::connection()->query($sql)->fetchAll();
    }
}
