<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10_000;
        $chunkSize = 500;
        $now = now()->toDateTimeString();

        $descriptions = ['Window Seating', 'Corner Table', 'Center Dining', 'Private Area', 'Bar Counter', 'Outdoor Patio', 'Indoor Lounge', 'VIP Section'];
        $sections = ['Main Hall', 'Patio', 'Bar', 'Lounge', 'Private Room'];

        $this->command->info("Seeding {$total} tables in chunks of {$chunkSize}...");
        $bar = $this->command->getOutput()->createProgressBar($total / $chunkSize);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $rows = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $n = $i + $j + 1;
                $section = $sections[($n - 1) % count($sections)];
                $rows[] = [
                    'table_name'      => $section . ' - Table ' . str_pad($n, 5, '0', STR_PAD_LEFT),
                    'description'     => $descriptions[($n - 1) % count($descriptions)],
                    'max_seats'       => 2 + ($n % 6),
                    'last_accessed_by' => 'Administrator',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
            DB::table('restaurant_tables')->insert($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done! ' . number_format($total) . ' tables seeded.');
    }
}
