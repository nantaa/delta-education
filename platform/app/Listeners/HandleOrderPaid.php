<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Webinar;
use App\Models\Product;
use App\Models\Registration;
use App\Jobs\SendRegistrationConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleOrderPaid implements ShouldQueue
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        foreach ($order->items as $item) {
            if ($item->productable_type === Webinar::class) {
                
                // Ensure no duplicates
                $exists = Registration::where('webinar_id', $item->productable_id)
                    ->where('email', $order->customer_email)
                    ->exists();

                if (!$exists) {
                    $registration = Registration::create([
                        'webinar_id' => $item->productable_id,
                        'name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                    ]);

                    SendRegistrationConfirmation::dispatch($registration);
                }

            } elseif ($item->productable_type === Product::class) {
                // Phase 4 functionality: Grant Ebook access
                Log::info("Ebook access granted for User/Email {$order->customer_email} - Product {$item->productable_id}");
            }
        }
    }
}
