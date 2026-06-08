<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Step 1: Seed all roles and permissions ──
        $this->call(RolesAndPermissionsSeeder::class);

        // ── Step 2: Create the default Super Admin user ──
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@dineflow.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Self-referencing restaurant_id for the owner
        if (is_null($superAdmin->restaurant_id)) {
            $superAdmin->restaurant_id = $superAdmin->id;
            $superAdmin->save();
        }

        $superAdmin->syncRoles(['Super Admin']);

        // ── Step 3: Create a test user (legacy, kept for dev convenience) ──
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'restaurant_id' => $superAdmin->id,
            ]
        );
        $testUser->syncRoles(['Admin']);

        // ── Step 4: Seed sample data ──
        $this->call(DummyDataSeeder::class);

        $this->command->info('✅ Database seeded. Super Admin: admin@dineflow.com / password');
    }
}
