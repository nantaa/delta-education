<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;

class SendRegistrationConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Participant $participant)
    {
    }

    /**
     * Execute the job.
     * Phase 6: integrate WABLAS / email notification here.
     */
    public function handle(): void
    {
        Log::info('Stub: Sending registration confirmation to ' . $this->participant->email);
        // TODO Phase 6: send WhatsApp/email via WABLAS or Mailgun
    }
}
