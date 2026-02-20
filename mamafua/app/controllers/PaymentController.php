<?php

declare(strict_types=1);

require_once __DIR__ . '/../helpers/common.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Booking.php';

class PaymentController
{
    public static function stkPush(array $payload): array
    {
        $config = appConfig()['mpesa'];
        $token = self::accessToken($config);

        $timestamp = date('YmdHis');
        $password = base64_encode($config['shortcode'] . $config['passkey'] . $timestamp);

        $body = [
            'BusinessShortCode' => $config['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $payload['amount'],
            'PartyA' => $payload['phone_number'],
            'PartyB' => $config['shortcode'],
            'PhoneNumber' => $payload['phone_number'],
            'CallBackURL' => $config['callback_url'],
            'AccountReference' => 'Mamafua Booking ' . $payload['booking_id'],
            'TransactionDesc' => 'Mamafua household service booking',
        ];

        $response = self::request($config['stk_url'], $body, $token);
        $checkoutRequestId = $response['CheckoutRequestID'] ?? ('SIM-' . uniqid());
        $merchantRequestId = $response['MerchantRequestID'] ?? ('SIM-M-' . uniqid());

        Payment::createPending([
            'booking_id' => $payload['booking_id'],
            'phone_number' => $payload['phone_number'],
            'amount' => $payload['amount'],
            'checkout_request_id' => $checkoutRequestId,
            'merchant_request_id' => $merchantRequestId,
            'raw_response' => json_encode($response),
        ]);

        return [
            'checkout_request_id' => $checkoutRequestId,
            'merchant_request_id' => $merchantRequestId,
            'response' => $response,
        ];
    }

    private static function accessToken(array $config): string
    {
        if (empty($config['consumer_key']) || empty($config['consumer_secret'])) {
            return '';
        }

        $ch = curl_init($config['token_url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Basic ' . base64_encode($config['consumer_key'] . ':' . $config['consumer_secret'])],
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        $json = json_decode((string) $raw, true);
        return $json['access_token'] ?? '';
    }

    private static function request(string $url, array $body, string $token): array
    {
        if ($token === '' || $url === '') {
            return ['ResponseCode' => '0', 'ResponseDescription' => 'Sandbox simulation accepted'];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POSTFIELDS => json_encode($body),
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        return json_decode((string) $raw, true) ?? ['raw' => $raw];
    }
}
