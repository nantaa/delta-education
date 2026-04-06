<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the `options` table from docs/options_dump.txt
 *
 * Format of the file:
 *   JOB:value|label  → type = 'job', value = numeric id, label = text
 *   SRC:id|source    → type = 'source', value = numeric id, label = text
 *
 * Run with:  php artisan db:seed --class=OptionsSeeder
 */
class OptionsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path('docs/options_dump.txt');

        if (! file_exists($filePath)) {
            $this->command->error("File not found: {$filePath}");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $jobs    = [];
        $sources = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip header lines  (e.g. "JOB:value|label" or "SRC:id|source")
            if (
                str_starts_with($line, 'JOB:value') ||
                str_starts_with($line, 'SRC:id')
            ) {
                continue;
            }

            if (str_starts_with($line, 'JOB:')) {
                $rest = substr($line, 4); // strip "JOB:"
                [$value, $label] = explode('|', $rest, 2);
                $jobs[] = [
                    'type'       => 'job',
                    'value'      => trim($value),
                    'label'      => trim($label),
                    'sort_order' => (int) trim($value),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } elseif (str_starts_with($line, 'SRC:')) {
                $rest = substr($line, 4); // strip "SRC:"
                [$value, $label] = explode('|', $rest, 2);
                $sources[] = [
                    'type'       => 'source',
                    'value'      => trim($value),
                    'label'      => trim($label),
                    'sort_order' => (int) trim($value),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Wipe and re-seed
        DB::table('options')->where('type', 'job')->delete();
        DB::table('options')->where('type', 'source')->delete();

        DB::table('options')->insert($jobs);
        DB::table('options')->insert($sources);

        $this->command->info('✅ Seeded ' . count($jobs) . ' job options.');
        $this->command->info('✅ Seeded ' . count($sources) . ' source options.');
    }
}
