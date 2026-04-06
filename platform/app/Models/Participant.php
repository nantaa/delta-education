<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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

    // ─── Static option helpers ─────────────────────────────────────────────────

    public static function jobOptions(): array
    {
        static $cache = null;
        $cache ??= self::loadExcelOptions(base_path('docs/job_options.xlsx'));
        return $cache;
    }

    public static function sourceOptions(): array
    {
        static $cache = null;
        $cache ??= self::loadExcelOptions(base_path('docs/source_options.xlsx'));
        return $cache;
    }

    private static function loadExcelOptions(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }
        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);
            $rows = $reader->load($path)->getActiveSheet()->toArray();
            $result = [];
            foreach (array_slice($rows, 1) as $row) {
                if (! empty($row[1])) {
                    $result[(string) $row[0]] = (string) $row[1];
                }
            }
            return $result;
        } catch (\Throwable) {
            return [];
        }
    }
}
