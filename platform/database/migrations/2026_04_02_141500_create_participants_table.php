<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            // Polymorphic relation to Webinar, etc.
            $table->morphs('participatable');
            $table->string('name');
            $table->unsignedTinyInteger('age')->nullable();
            $table->text('background')->nullable();
            $table->string('email')->unique();
            $table->string('gender', 10)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('domicile')->nullable();
            $table->string('last_education')->nullable();
            // education_status: 'Sedang Menempuh' | 'Tidak Sedang Menempuh'
            $table->string('education_status', 30)->default('Tidak Sedang Menempuh');
            $table->string('institution_name')->nullable();    // shown when education_status = Sedang Menempuh
            // employment_status: 'Sedang Bekerja' | 'Tidak Sedang Bekerja'
            $table->string('employment_status', 30)->default('Tidak Sedang Bekerja');
            $table->string('current_job')->nullable();        // dropdown – job_options.xlsx
            $table->string('company')->nullable();
            $table->string('event_source')->nullable();       // dropdown – source_options.xlsx
            $table->boolean('privacy_consent')->default(false);
            // Payment (follows Midtrans / webinar-registration best practice)
            $table->string('payment_method', 30)->default('none');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('payment_status', 20)->default('pending');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};

