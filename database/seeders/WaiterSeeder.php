<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaiterSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10_000;
        $chunkSize = 500;
        $now = now()->toDateTimeString();

        $firstNames = ['Raj', 'Priya', 'Amit', 'Sneha', 'Vikram', 'Anjali', 'Arjun', 'Pooja', 'Rohan', 'Neha'];
        $lastNames = ['Sharma', 'Singh', 'Patel', 'Kumar', 'Gupta', 'Reddy', 'Verma', 'Agarwal', 'Chopra', 'Desai'];

        $this->command->info("Seeding {$total} waiters in chunks of {$chunkSize}...");
        $bar = $this->command->getOutput()->createProgressBar($total / $chunkSize);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $rows = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $n = $i + $j + 1;
                $firstName = $firstNames[($n - 1) % count($firstNames)];
                $lastName = $lastNames[($n - 1) % count($lastNames)];
                
                $rows[] = [
                    'waiter_code'     => 'W' . str_pad($n, 6, '0', STR_PAD_LEFT),
                    'name'            => $firstName . ' ' . $lastName . ' ' . $n,
                    'mobile'          => '9' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                    'dob'             => date('Y-m-d', strtotime((-25 - ($n % 15)) . ' years')),
                    'last_accessed_by' => 'Administrator',
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }
            DB::table('waiters')->insert($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done! ' . number_format($total) . ' waiters seeded.');
    }
}
