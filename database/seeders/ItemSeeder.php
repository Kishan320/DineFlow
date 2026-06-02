<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10_000;
        $chunkSize = 500;
        $now = now()->toDateTimeString();

        $items = [
            'Biryani', 'Butter Chicken', 'Paneer Tikka', 'Tandoori Chicken', 'Samosa',
            'Dosa', 'Idli', 'Uttapam', 'Masala Chai', 'Coffee',
            'Lassi', 'Juice', 'Naan', 'Roti', 'Rice',
            'Dal', 'Vegetables', 'Fish', 'Shrimp', 'Lamb',
            'Dessert', 'Ice Cream', 'Kheer', 'Gulab Jamun', 'Jalebi'
        ];

        $categories = ['Non Veg', 'Veg', 'Beverages', 'Bread', 'Rice', 'Desserts', 'Appetizers'];
        $taxTypes = ['Exclusive', 'Inclusive'];
        $states = ['On Sale', 'Off Sale'];
        $itemTypes = ['Physical Item', 'Digital Item', 'Service'];

        $this->command->info("Seeding {$total} items in chunks of {$chunkSize}...");
        $bar = $this->command->getOutput()->createProgressBar($total / $chunkSize);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $rows = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $n = $i + $j + 1;
                $basePrice = 50 + ($n % 1000);
                
                $rows[] = [
                    'code'              => 'ITEM' . str_pad($n, 6, '0', STR_PAD_LEFT),
                    'item_name'         => $items[($n - 1) % count($items)] . ' #' . $n,
                    'category'          => $categories[($n - 1) % count($categories)],
                    'restaurant_price'  => $basePrice,
                    'bar_price'         => $basePrice + 50,
                    'room_price'        => $basePrice + 100,
                    'tax_type'          => $taxTypes[($n - 1) % count($taxTypes)],
                    'tax'               => 'TAX' . str_pad($n % 100, 3, '0', STR_PAD_LEFT),
                    'state'             => $states[($n - 1) % 2 == 0 ? 0 : 1],
                    'item_type'         => $itemTypes[($n - 1) % count($itemTypes)],
                    'note'              => 'Item #' . $n . ' description',
                    'image_url'         => null,
                    'last_accessed_by'  => 'Administrator',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
            DB::table('items')->insert($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done! ' . number_format($total) . ' items seeded.');
    }
}
