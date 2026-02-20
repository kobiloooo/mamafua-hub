<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/Payment.php';
require_once __DIR__ . '/../app/models/Booking.php';

$action = $_GET['action'] ?? '';

if ($action === 'callback') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];

    $stk = $payload['Body']['stkCallback'] ?? [];
    $checkoutRequestId = $stk['CheckoutRequestID'] ?? '';
    $resultCode = (int) ($stk['ResultCode'] ?? 1);

    if ($checkoutRequestId === '') {
        jsonResponse(['ResultCode' => 1, 'ResultDesc' => 'Missing checkout id'], 400);
    }

    if ($resultCode === 0) {
        $items = $stk['CallbackMetadata']['Item'] ?? [];
        $receipt = 'NA';
        foreach ($items as $item) {
            if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                $receipt = $item['Value'] ?? 'NA';
            }
        }

        $payment = Payment::markPaid($checkoutRequestId, $receipt);
        if ($payment) {
            Booking::updatePaymentStatus((int) $payment['booking_id'], 'paid', 'confirmed');
        }
    } else {
        $payment = Payment::markFailed($checkoutRequestId, $payload);
        if ($payment) {
            Booking::updatePaymentStatus((int) $payment['booking_id'], 'failed', 'cancelled');
        }
    }

    jsonResponse(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
}

if ($action === 'timeout') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    file_put_contents(__DIR__ . '/../storage/mpesa_timeout.log', date('c') . ' ' . json_encode($payload) . PHP_EOL, FILE_APPEND);
    jsonResponse(['ResultCode' => 0, 'ResultDesc' => 'Timeout logged']);
}

if ($action === 'history') {
    requireAuth(['admin']);
    jsonResponse(['success' => true, 'data' => Payment::all()]);
}

jsonResponse(['success' => false, 'message' => 'Unknown payments action.'], 404);
