<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/models/Mamafua.php';
require_once __DIR__ . '/../app/models/Review.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';

$action = $_GET['action'] ?? '';

if ($action === 'create') {
    $user = requireAuth(['client']);
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 422);
    }

    $mamafuaId = (int) ($_POST['mamafua_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $data = [
        'client_id' => $user['id'],
        'mamafua_id' => $mamafuaId,
        'service_type' => sanitize($_POST['service_type'] ?? ''),
        'pricing_plan' => sanitize($_POST['pricing_plan'] ?? 'hourly'),
        'booking_date' => sanitize($_POST['booking_date'] ?? ''),
        'start_time' => sanitize($_POST['start_time'] ?? ''),
        'end_time' => sanitize($_POST['end_time'] ?? ''),
        'location' => sanitize($_POST['location'] ?? ''),
        'amount' => $amount,
    ];

    if (!Booking::slotAvailable($mamafuaId, $data['booking_date'], $data['start_time'], $data['end_time'])) {
        jsonResponse(['success' => false, 'message' => 'This time slot is already booked. Pick another time.'], 409);
    }

    $bookingId = Booking::create($data);
    $payment = PaymentController::stkPush([
        'booking_id' => $bookingId,
        'phone_number' => sanitize($_POST['phone_number'] ?? ''),
        'amount' => $amount,
    ]);

    jsonResponse(['success' => true, 'message' => 'Booking created. Complete M-Pesa prompt on your phone.', 'booking_id' => $bookingId, 'payment' => $payment]);
}

if ($action === 'mine') {
    $user = requireAuth(['client', 'mamafua']);
    $rows = Booking::byUser($user['id'], $user['role']);
    jsonResponse(['success' => true, 'data' => $rows]);
}

if ($action === 'status') {
    $user = requireAuth(['client', 'mamafua']);
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 422);
    }

    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'confirmed');
    if (!in_array($status, ['confirmed', 'cancelled', 'rescheduled', 'completed', 'disputed'], true)) {
        jsonResponse(['success' => false, 'message' => 'Invalid status.'], 422);
    }
    Booking::updateStatus($bookingId, $status);
    jsonResponse(['success' => true, 'message' => 'Booking updated.']);
}

if ($action === 'review') {
    $user = requireAuth(['client']);
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 422);
    }

    Review::create([
        'booking_id' => (int) ($_POST['booking_id'] ?? 0),
        'client_id' => $user['id'],
        'mamafua_id' => (int) ($_POST['mamafua_id'] ?? 0),
        'rating' => max(1, min(5, (int) ($_POST['rating'] ?? 5))),
        'comment' => sanitize($_POST['comment'] ?? ''),
    ]);

    jsonResponse(['success' => true, 'message' => 'Asante! Your review is awaiting moderation.']);
}

jsonResponse(['success' => false, 'message' => 'Unknown booking action.'], 404);
