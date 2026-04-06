<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'participatable_id', 'participatable_type',
        'name', 'age', 'background', 'email', 'gender',
        'whatsapp_number', 'domicile', 'last_education',
        'education_status', 'institution_name',
        'employment_status', 'current_job', 'company',
        'event_source', 'privacy_consent',
        'payment_method', 'transaction_id', 'payment_status', 'amount_paid',
        'discount_code_id', 'discount_amount',
    ];

    protected $casts = [
        'privacy_consent' => 'boolean',
        'amount_paid'     => 'decimal:2',
        'age'             => 'integer',
    ];

    public function participatable()
    {
        return $this->morphTo();
    }

    // ─── Static option helpers (read from DB options table) ───────────────────

    public static function jobOptions(): array
    {
        return Cache::remember('options.job', 3600, function () {
            return DB::table('options')
                ->where('type', 'job')
                ->orderBy('label')
                ->pluck('label', 'value')
                ->toArray();
        });
    }

    public static function sourceOptions(): array
    {
        return Cache::remember('options.source', 3600, function () {
            return DB::table('options')
                ->where('type', 'source')
                ->orderBy('sort_order')
                ->pluck('label', 'value')
                ->toArray();
        });
    }
}
