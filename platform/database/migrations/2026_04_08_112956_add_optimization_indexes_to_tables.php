<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->index(['status', 'scheduled_at'], 'idx_webinars_sts_sch');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->index(['status', 'scheduled_at'], 'idx_trainings_sts_sch');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'idx_orders_status');
            $table->index('created_at', 'idx_orders_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropIndex('idx_webinars_sts_sch');
        });

        Schema::table('trainings', function (Blueprint $table) {
            $table->dropIndex('idx_trainings_sts_sch');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
        });
    }
};
