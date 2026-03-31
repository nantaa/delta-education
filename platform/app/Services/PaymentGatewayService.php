<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('services.midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = config('services.midtrans.is_production', false);
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;
    }

    /**
     * Get SNAP Token from Midtrans
     *
     * @param Order $order
     * @return string|null
     */
    public function getSnapToken(Order $order): ?string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->id . '-' . time(), // Unique ID per attempt
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ]
        ];

        // Format Items for Midtrans
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id' => $item->productable_id,
                'price' => (int) $item->price,
                'quantity' => 1,
                'name' => substr($item->item_name, 0, 50) // Midtrans max length is 50
            ];
        }

        if (count($itemDetails) > 0) {
            $params['item_details'] = $itemDetails;
        }

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return null;
        }
    }
}
