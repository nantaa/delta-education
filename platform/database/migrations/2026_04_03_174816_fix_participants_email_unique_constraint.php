<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // email unique was already removed; add composite: one person per event
        \Illuminate\Support\Facades\DB::statement(
            'ALTER TABLE participants ADD UNIQUE participants_email_event_unique (email(100), participatable_type(100), participatable_id)'
        );
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            'ALTER TABLE participants DROP INDEX participants_email_event_unique'
        );
    }
};
