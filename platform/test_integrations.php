<?php

/**
 * Quick test script for Midtrans and Gmail SMTP.
 * Run: php artisan tinker < test_integrations.php
 * or:  php test_integrations.php (from platform root after bootstrapping)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// ─────────────────────────────────────────────
// 1. TEST GMAIL SMTP
// ─────────────────────────────────────────────
echo "── Gmail SMTP Test ──\n";
try {
    \Illuminate\Support\Facades\Mail::raw(
        'Ini adalah email tes dari sistem Delta Education. Jika Anda menerima email ini, konfigurasi Gmail SMTP berfungsi dengan benar.',
        function ($message) {
            $message->to(config('mail.from.address'))
                    ->subject('[TEST] Delta Education - Gmail SMTP OK · ' . now()->format('d M Y H:i'));
        }
    );
    echo "✅ Gmail SMTP: Email terkirim ke " . config('mail.from.address') . "\n";
} catch (\Exception $e) {
    echo "❌ Gmail SMTP Error: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────
// 2. TEST MIDTRANS CONFIG
// ─────────────────────────────────────────────
echo "\n── Midtrans Config Test ──\n";

$serverKey   = config('services.midtrans.server_key');
$clientKey   = config('services.midtrans.client_key');
$isProduction = config('services.midtrans.is_production');

echo "  Server Key : " . ($serverKey   ? substr($serverKey, 0, 20) . '...' : '❌ NOT SET') . "\n";
echo "  Client Key : " . ($clientKey   ? substr($clientKey, 0, 20) . '...' : '❌ NOT SET') . "\n";
echo "  Mode       : " . ($isProduction ? '🔴 PRODUCTION' : '🟡 SANDBOX') . "\n";

if ($serverKey && $clientKey) {
    \Midtrans\Config::$serverKey   = $serverKey;
    \Midtrans\Config::$isProduction = $isProduction;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds       = true;
    \Midtrans\Config::$curlOptions = [
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    ];

    try {
        // Ping Midtrans status endpoint
        $params = [
            'transaction_details' => [
                'order_id'    => 'TEST-' . time(),
                'gross_amount' => 10000,
            ],
            'customer_details' => [
                'first_name' => 'Test',
                'email'      => 'test@example.com',
                'phone'      => '081234567890',
            ],
        ];
        $token = \Midtrans\Snap::getSnapToken($params);
        echo "✅ Midtrans: Snap Token diperoleh → " . substr($token, 0, 30) . "...\n";
    } catch (\Exception $e) {
        echo "❌ Midtrans Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Midtrans: Server/Client key tidak ditemukan di .env\n";
}

echo "\n── Selesai ──\n";
