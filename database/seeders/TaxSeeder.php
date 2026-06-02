<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10_000;
        $chunkSize = 500;
        $now = now()->toDateTimeString();

        $descriptions = [
            'Food Items',
            'Beverages',
            'Snacks',
            'Desserts',
            'Dairy Products',
            'Spices',
            'Condiments',
            'Frozen Foods',
            'Organic Items',
            'Premium Foods'
        ];

        $this->command->info("Seeding {$total} taxes in chunks of {$chunkSize}...");
        $bar = $this->command->getOutput()->createProgressBar($total / $chunkSize);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $rows = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $n = $i + $j + 1;
                $cgst = 5 + (($n - 1) % 3) * 4.5;
                $sgst = 5 + (($n - 1) % 3) * 4.5;
                $igst = 9 + (($n - 1) % 3) * 9;
                $taxPercent = ($cgst + $sgst) / 2;

                $rows[] = [
                    'hsn_code'        => str_pad($n % 10000, 4, '0', STR_PAD_LEFT) . str_pad($n % 100, 2, '0', STR_PAD_LEFT),
                    'description'     => $descriptions[($n - 1) % count($descriptions)] . ' #' . $n,
                    'cgst'            => $cgst,
                    'sgst'            => $sgst,
                    'igst'            => $igst,
                    'cess'            => rand(0, 1) ? rand(0, 50) / 100 : 0,
                    'additional_cess' => rand(0, 1) ? rand(0, 50) / 100 : 0,
                    'vat'             => 5 + ($n % 10),
                    'tax_percent'     => $taxPercent,
                    'last_accessed_by' => 'Administrator',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
            DB::table('taxes')->insert($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done! ' . number_format($total) . ' taxes seeded.');
    }
}
