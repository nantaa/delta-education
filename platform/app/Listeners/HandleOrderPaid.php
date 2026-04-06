<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Webinar;
use App\Models\Participant;
use App\Jobs\SendRegistrationConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class HandleOrderPaid implements ShouldQueue
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        foreach ($order->items as $item) {
            if ($item->productable_type === Webinar::class) {
                $webinar = Webinar::find($item->productable_id);
                if (! $webinar) {
                    continue;
                }

                // Avoid duplicate participants
                $exists = $webinar->participants()
                    ->where('email', $order->customer_email)
                    ->exists();

                if (! $exists) {
                    $participant = $webinar->participants()->create([
                        'name'           => $order->customer_name,
                        'email'          => $order->customer_email,
                        'whatsapp_number'=> $order->customer_phone,
                        'payment_status' => 'settlement',
                        'payment_method' => 'midtrans',
                        'amount_paid'    => $item->price,
                        'privacy_consent'=> true,
                    ]);

                    SendRegistrationConfirmation::dispatch($participant);
                }
            } else {
                // Ebooks / mini-courses: Phase 6 (grant access)
                Log::info("Ebook/product access pending — email {$order->customer_email}, product {$item->productable_id}");
            }
        }
    }
}
