<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryBulkSeeder extends Seeder
{
    public function run(): void
    {
        $total     = 1_000;
        $chunkSize = 5_0;
        $now       = now()->toDateTimeString();

        $accessors = ['Administrator', 'Manager', 'Supervisor', 'Staff', 'Owner'];
        $prefixes  = ['Food', 'Beverage', 'Snack', 'Dessert', 'Starter', 'Main', 'Side', 'Special', 'Seasonal', 'Chef'];
        $suffixes  = ['Items', 'Dishes', 'Picks', 'Specials', 'Delights', 'Favorites', 'Classics', 'Bites', 'Treats', 'Platters'];

        $this->command->info("Seeding {$total} categories in chunks of {$chunkSize}...");
        $bar = $this->command->getOutput()->createProgressBar($total / $chunkSize);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $rows = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $n = $i + $j + 1;
                $rows[] = [
                    'category_name'    => $prefixes[($n - 1) % 10] . ' ' . $suffixes[(int)(($n - 1) / 10) % 10] . ' ' . $n,
                    'description'      => 'Category description for entry ' . $n,
                    'last_accessed_by' => $accessors[$n % 5],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
            DB::table('categories')->insert($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done! ' . number_format($total) . ' categories seeded.');
    }
}
