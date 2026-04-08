<?php
$ch = curl_init('https://events.deltaindo.co.id/webhook/midtrans');
curl_setopt($ch, CURLOPT_POST, 1);
$payload = json_encode([
    'order_id' => '13-1775537673',
    'transaction_status' => 'settlement'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
echo "HTTP_CODE: $httpCode\n";
echo "ERROR: $error\n";
echo "RESPONSE_BODY: $res\n";
