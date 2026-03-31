<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Events\OrderPaid;

class MidtransController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received: ', $payload);

        $orderIdSplit = explode('-', $payload['order_id'] ?? '');
        $orderId = $orderIdSplit[0] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid order_id format'], 400);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Validate Signature Key
        $serverKey = config('services.midtrans.server_key');
        $validSignature = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);

        if ($validSignature !== ($payload['signature_key'] ?? '')) {
            Log::warning('Midtrans Invalid Signature for Order ID: ' . $orderId);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'];

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($order->status !== 'paid') {
                $order->update([
                    'status' => 'paid',
                ]);
                
                // Fire the event to grant access
                OrderPaid::dispatch($order);
            }
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $order->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
