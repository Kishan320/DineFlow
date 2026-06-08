<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Tax;
use App\Models\Customer;
use App\Models\Waiter;
use App\Models\RestaurantTable;
use App\Models\RestaurantSettings;
use App\Models\PosSession;
use App\Models\PosOrder;
use App\Models\PosOrderItem;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create two distinct users for data ownership
        $user1 = User::firstOrCreate(
            ['email' => 'dummy1@dineflow.com'],
            ['name' => 'Dummy User 1', 'password' => bcrypt('password')]
        );
        if (is_null($user1->restaurant_id)) {
            $user1->restaurant_id = $user1->id;
            $user1->save();
        }
        $user1->syncRoles(['Admin']);

        $user2 = User::firstOrCreate(
            ['email' => 'dummy2@dineflow.com'],
            ['name' => 'Dummy User 2', 'password' => bcrypt('password')]
        );
        if (is_null($user2->restaurant_id)) {
            $user2->restaurant_id = $user2->id;
            $user2->save();
        }
        $user2->syncRoles(['Admin']);

        $users = [$user1, $user2];

        $this->command->info('Fetching data from dummyjson API...');
        
        // 2. Fetch dummy json data
        $response = Http::get('https://dummyjson.com/products?limit=194');
        if (!$response->successful()) {
            $this->command->error('Failed to fetch from dummyjson API.');
            return;
        }

        $products = $response->json()['products'] ?? [];

        foreach ($users as $user) {
            $this->seedForUser($user, $products);
        }

        $this->command->info('Dummy data seeding completed!');
    }

    private function seedForUser(User $user, array $products)
    {
        $this->command->info("Seeding data for {$user->name}...");

        // Taxes
        $taxExclusive = Tax::firstOrCreate(
            ['created_by' => $user->id, 'hsn_code' => 'GST5-' . $user->id],
            ['description' => 'GST 5%', 'cgst' => 2.5, 'sgst' => 2.5, 'tax_percent' => 5, 'last_accessed_by' => $user->name]
        );

        // Restaurant Settings
        RestaurantSettings::updateOrCreate(
            ['created_by' => $user->id],
            [
                'business_unit' => 'BU-' . $user->id,
                'restaurant_name' => $user->name . ' Restaurant',
                'gst_no' => '22AAAAA0000A1Z5',
                'bill_footer_text' => 'Thank you for dining with us!',
                'guest_bill' => 'Enabled',
                'last_accessed_by' => $user->name
            ]
        );

        // Waiters
        $waiterIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $waiter = Waiter::firstOrCreate(
                ['created_by' => $user->id, 'waiter_code' => 'W' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                ['name' => 'Waiter ' . $i, 'mobile' => '987654321' . $i, 'last_accessed_by' => $user->name]
            );
            $waiterIds[] = $waiter->id;
        }

        // Tables
        $tableIds = [];
        for ($i = 1; $i <= 20; $i++) {
            $table = RestaurantTable::firstOrCreate(
                ['created_by' => $user->id, 'table_name' => 'Table ' . $i],
                ['max_seats' => rand(2, 6), 'status' => 'Available', 'last_accessed_by' => $user->name]
            );
            $tableIds[] = $table->id;
        }

        // Customers
        $customerIds = [];
        for ($i = 1; $i <= 50; $i++) {
            $customer = Customer::firstOrCreate(
                ['created_by' => $user->id, 'code' => 'CUST' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'company_name' => 'Customer ' . $i,
                    'mobile' => '88877766' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'last_accessed_by' => $user->name
                ]
            );
            $customerIds[] = $customer->id;
        }

        // Categories and Items from API
        $categoryCache = [];
        $itemIds = [];

        foreach ($products as $product) {
            $catName = ucwords(str_replace('-', ' ', $product['category'] ?? 'General'));
            
            if (!isset($categoryCache[$catName])) {
                $category = Category::firstOrCreate(
                    ['created_by' => $user->id, 'category_name' => $catName],
                    ['description' => $catName . ' category', 'last_accessed_by' => $user->name]
                );
                $categoryCache[$catName] = $category->id;
            }

            $sku = $product['sku'] ?? 'SKU-' . $product['id'];

            $item = Item::firstOrCreate(
                ['created_by' => $user->id, 'code' => $sku],
                [
                    'item_name' => $product['title'],
                    'category' => $catName,
                    'restaurant_price' => $product['price'],
                    'bar_price' => $product['price'],
                    'room_price' => $product['price'],
                    'tax_type' => 'Exclusive',
                    'tax' => $taxExclusive->id,
                    'state' => 'On Sale',
                    'item_type' => 'Physical Item',
                    'note' => substr($product['description'], 0, 255), // Limit length
                    'image_url' => $product['thumbnail'] ?? null,
                    'last_accessed_by' => $user->name
                ]
            );
            $itemIds[] = $item->id;
        }

        if (empty($itemIds)) return;

        // POS Sessions & Orders to populate Reports
        // Create sessions spread over the past 90 days for robust report data (every day)
        for ($daysBack = 90; $daysBack >= 0; $daysBack--) { 
            $date = Carbon::now()->subDays($daysBack);
            
            $sessionCode = 'SESS-' . $user->id . '-' . $date->format('Ymd');
            $session = PosSession::firstOrCreate(
                ['session_code' => $sessionCode],
                [
                    'user_id' => $user->id,
                    'counter_name' => 'Main Counter',
                    'branch_name' => 'Main Branch',
                    'opening_balance' => 1000,
                    'closing_balance' => null, 
                    'status' => 'Open',
                    'opened_at' => $date->copy()->setTime(9, 0, 0),
                    'created_at' => $date,
                    'updated_at' => $date,
                    'last_accessed_by' => $user->name
                ]
            );

            // Create 10 to 20 orders for this session to ensure rich data density for filters
            $numOrders = rand(10, 20);
            $sessionTotal = 0;

            for ($o = 1; $o <= $numOrders; $o++) {
                $orderDate = $date->copy()->addHours(rand(1, 10));
                
                $customerId = $customerIds[array_rand($customerIds)];
                $customer = Customer::find($customerId);
                
                $tableId = $tableIds[array_rand($tableIds)];
                $table = RestaurantTable::find($tableId);
                
                $waiterId = $waiterIds[array_rand($waiterIds)];
                $waiter = Waiter::find($waiterId);

                $orderTypes = ['Dine In', 'Takeaway', 'Delivery'];
                $statuses = ['Completed', 'Completed', 'Completed', 'Pending', 'Confirmed', 'Preparing', 'Ready', 'Served', 'Cancelled']; // Weighted towards Completed
                $paymentStatuses = ['Paid', 'Paid', 'Paid', 'Unpaid', 'Partial'];
                $billPayTypes = ['Cash', 'Card', 'UPI', 'Others'];

                $selOrderType = $orderTypes[array_rand($orderTypes)];
                $selStatus = $statuses[array_rand($statuses)];
                $selPaymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                $selBillPayType = $billPayTypes[array_rand($billPayTypes)];

                $orderNo = 'ORD-' . $user->id . '-' . $date->format('Ymd') . '-' . str_pad($o, 3, '0', STR_PAD_LEFT);

                $order = PosOrder::firstOrCreate(
                    ['order_no' => $orderNo],
                    [
                        'created_by' => $user->id,
                        'session_id' => $session->id,
                        'customer_id' => $customerId,
                        'customer_name' => $customer->company_name,
                        'customer_phone' => $customer->mobile,
                        'table_id' => $tableId,
                        'table_label' => $table->table_name,
                        'waiter_id' => $waiterId,
                        'waiter_name' => $waiter->name,
                        'order_type' => $selOrderType,
                        'covers' => rand(1, 6),
                        'status' => $selStatus,
                        'payment_status' => $selPaymentStatus,
                        'bill_pay_type' => $selBillPayType,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                        'last_accessed_by' => $user->name
                    ]
                );

                // Create 1 to 4 items per order (only if they don't exist for this order)
                if ($order->wasRecentlyCreated) {
                    $numItems = rand(1, 4);
                    $subtotal = 0;
                    $taxAmount = 0;

                    for ($i = 0; $i < $numItems; $i++) {
                        $itemId = $itemIds[array_rand($itemIds)];
                        $item = Item::find($itemId);
                        $qty = rand(1, 3);
                        $unitPrice = $item->restaurant_price;
                        $lineTotal = $unitPrice * $qty;
                        $lineTax = $lineTotal * 0.05;

                        PosOrderItem::create([
                            'pos_order_id' => $order->id,
                            'item_id' => $item->id,
                            'item_name' => $item->item_name,
                            'item_code' => $item->code,
                            'category' => $item->category,
                            'unit_price' => $unitPrice,
                            'quantity' => $qty,
                            'tax_amount' => $lineTax,
                            'tax_percent' => 5,
                            'tax_type' => 'Exclusive',
                            'line_total' => $lineTotal + $lineTax,
                            'created_at' => $orderDate,
                            'updated_at' => $orderDate,
                        ]);

                        $subtotal += $lineTotal;
                        $taxAmount += $lineTax;
                    }

                    $total = $subtotal + $taxAmount;
                    $order->update([
                        'subtotal' => $subtotal,
                        'tax_amount' => $taxAmount,
                        'total' => $total,
                        'net_payable' => $total,
                        'cash_amt' => $total,
                    ]);

                    $sessionTotal += $total;
                }
            }

            // Close session
            $session->update([
                'closing_balance' => 1000 + $sessionTotal,
                'total_sales' => $sessionTotal,
                'total_cash' => $sessionTotal,
                'status' => 'Closed',
                'closed_at' => $date->copy()->setTime(22, 0, 0)
            ]);
        }
    }
}
