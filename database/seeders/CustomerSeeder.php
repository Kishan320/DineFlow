<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10_000;
        $chunkSize = 500;
        $now = now()->toDateTimeString();

        $companies = ['ABC Corp', 'XYZ Inc', 'Tech Solutions', 'Global Trade', 'Prime Business', 'Elite Enterprises', 'Smart Ventures', 'Dynamic Group'];
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Hyderabad', 'Pune', 'Kolkata', 'Ahmedabad', 'Jaipur', 'Lucknow'];
        $countries = ['India', 'USA', 'UK', 'Canada', 'Australia', 'Germany', 'France', 'Singapore'];
        $terms = ['Net 30', 'Net 60', 'COD', 'Prepaid', 'Net 15'];

        $this->command->info("Seeding {$total} customers in chunks of {$chunkSize}...");
        $bar = $this->command->getOutput()->createProgressBar($total / $chunkSize);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $rows = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $n = $i + $j + 1;
                $rows[] = [
                    'code'              => 'CUST' . str_pad($n, 6, '0', STR_PAD_LEFT),
                    'company_name'      => $companies[($n - 1) % count($companies)] . ' ' . $n,
                    'contact_person'    => 'Contact Person ' . $n,
                    'email'             => 'customer' . $n . '@example.com',
                    'mobile'            => '9' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                    'tax_number'        => 'TAX' . str_pad($n, 8, '0', STR_PAD_LEFT),
                    'payment_terms'     => $terms[($n - 1) % count($terms)],
                    'billing_name'      => 'Bill Name ' . $n,
                    'billing_address'   => $n . ' Business Street',
                    'billing_address2'  => 'Suite ' . str_pad($n % 100, 2, '0', STR_PAD_LEFT),
                    'billing_city'      => $cities[($n - 1) % count($cities)],
                    'billing_state'     => 'State ' . (($n - 1) % 5 + 1),
                    'billing_country'   => $countries[($n - 1) % count($countries)],
                    'billing_zip'       => str_pad($n % 100000, 6, '0', STR_PAD_LEFT),
                    'same_as_billing'   => rand(0, 1),
                    'shipping_name'     => 'Ship Name ' . $n,
                    'shipping_address'  => $n . ' Delivery Lane',
                    'shipping_address2' => 'Apt ' . str_pad($n % 200, 3, '0', STR_PAD_LEFT),
                    'shipping_city'     => $cities[($n - 1) % count($cities)],
                    'shipping_state'    => 'State ' . (($n - 1) % 5 + 1),
                    'shipping_country'  => $countries[($n - 1) % count($countries)],
                    'shipping_zip'      => str_pad($n % 100000, 6, '0', STR_PAD_LEFT),
                    'notes'             => 'Customer #' . $n . ' notes',
                    'last_accessed_by'  => 'Administrator',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
            DB::table('customers')->insert($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done! ' . number_format($total) . ' customers seeded.');
    }
}
