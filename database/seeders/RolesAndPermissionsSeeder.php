<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * All 43 permissions derived from actual controller analysis of DineFlow.
     */
    private array $permissions = [
        // ── Dashboard ──
        'dashboard.view',

        // ── Categories ──
        'categories.view',
        'categories.create',
        'categories.edit',
        'categories.delete',

        // ── Items (Menu) ──
        'items.view',
        'items.create',
        'items.edit',
        'items.delete',

        // ── Customers ──
        'customers.view',
        'customers.create',
        'customers.edit',
        'customers.delete',

        // ── Tables ──
        'tables.view',
        'tables.create',
        'tables.edit',
        'tables.delete',

        // ── Waiters ──
        'waiters.view',
        'waiters.create',
        'waiters.edit',
        'waiters.delete',

        // ── Taxes ──
        'taxes.view',
        'taxes.create',
        'taxes.edit',
        'taxes.delete',

        // ── Restaurant Settings ──
        'settings.view',
        'settings.manage',

        // ── POS System ──
        'pos.access',
        'pos.session.view',
        'pos.session.open',
        'pos.session.close',
        'pos.session.delete',
        'pos.orders.view',
        'pos.orders.create',
        'pos.orders.update_status',
        'pos.orders.delete',
        'pos.kot.view',
        'pos.kot.create',
        'pos.kot.update_status',
        'pos.customers.search',
        'pos.customers.create',
        'pos.reports.view',

        // ── Reports ──
        'reports.cashier',
        'reports.daily_sales',
        'reports.detailed_sales',
        'reports.item_wise',
        'reports.export',

        // ── Sales Management ──
        'sales.view',
        'sales.export',

        // ── User & Role Management ──
        'roles.view',
        'roles.manage',
        'users.view',
        'users.manage',
    ];

    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Super Admin — bypass all (full access via gate)
        // ────────────────────────────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'sanctum']);
        $superAdmin->syncPermissions(Permission::where('guard_name', 'sanctum')->get());

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Admin — full access except super-admin meta actions
        // ────────────────────────────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'sanctum']);
        $admin->syncPermissions([
            'dashboard.view',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'items.view', 'items.create', 'items.edit', 'items.delete',
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'tables.view', 'tables.create', 'tables.edit', 'tables.delete',
            'waiters.view', 'waiters.create', 'waiters.edit', 'waiters.delete',
            'taxes.view', 'taxes.create', 'taxes.edit', 'taxes.delete',
            'settings.view', 'settings.manage',
            'pos.access',
            'pos.session.view', 'pos.session.open', 'pos.session.close', 'pos.session.delete',
            'pos.orders.view', 'pos.orders.create', 'pos.orders.update_status', 'pos.orders.delete',
            'pos.kot.view', 'pos.kot.create', 'pos.kot.update_status',
            'pos.customers.search', 'pos.customers.create',
            'pos.reports.view',
            'reports.cashier', 'reports.daily_sales', 'reports.detailed_sales',
            'reports.item_wise', 'reports.export',
            'sales.view', 'sales.export',
            'roles.view', 'roles.manage',
            'users.view', 'users.manage',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Restaurant Manager — operational management (no user/role mgmt)
        // ────────────────────────────────────────────────────────────────────
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $manager->syncPermissions([
            'dashboard.view',
            'categories.view', 'categories.create', 'categories.edit',
            'items.view', 'items.create', 'items.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'tables.view', 'tables.create', 'tables.edit',
            'waiters.view', 'waiters.create', 'waiters.edit',
            'taxes.view',
            'settings.view',
            'pos.access',
            'pos.session.view', 'pos.session.open', 'pos.session.close',
            'pos.orders.view', 'pos.orders.create', 'pos.orders.update_status', 'pos.orders.delete',
            'pos.kot.view', 'pos.kot.create', 'pos.kot.update_status',
            'pos.customers.search', 'pos.customers.create',
            'pos.reports.view',
            'reports.cashier', 'reports.daily_sales', 'reports.detailed_sales', 'reports.item_wise',
            'sales.view',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Cashier — POS billing, payments, limited reports
        // ────────────────────────────────────────────────────────────────────
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'sanctum']);
        $cashier->syncPermissions([
            'dashboard.view',
            'customers.view', 'customers.create',
            'pos.access',
            'pos.session.view', 'pos.session.open', 'pos.session.close',
            'pos.orders.view', 'pos.orders.create', 'pos.orders.update_status',
            'pos.kot.view', 'pos.kot.create',
            'pos.customers.search', 'pos.customers.create',
            'pos.reports.view',
            'sales.view',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Kitchen Staff — views and updates KOT status only
        // ────────────────────────────────────────────────────────────────────
        $kitchen = Role::firstOrCreate(['name' => 'Kitchen Staff', 'guard_name' => 'sanctum']);
        $kitchen->syncPermissions([
            'pos.kot.view',
            'pos.kot.update_status',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Waiter — takes orders, views tables, updates order status
        // ────────────────────────────────────────────────────────────────────
        $waiter = Role::firstOrCreate(['name' => 'Waiter', 'guard_name' => 'sanctum']);
        $waiter->syncPermissions([
            'pos.access',
            'pos.orders.view', 'pos.orders.create', 'pos.orders.update_status',
            'pos.kot.view', 'pos.kot.create',
            'pos.customers.search',
        ]);

        // ────────────────────────────────────────────────────────────────────
        // ROLE: Delivery Staff — handles delivery orders only
        // ────────────────────────────────────────────────────────────────────
        $delivery = Role::firstOrCreate(['name' => 'Delivery Staff', 'guard_name' => 'sanctum']);
        $delivery->syncPermissions([
            'pos.access',
            'pos.orders.view', 'pos.orders.update_status',
            'pos.customers.search',
        ]);

        $delivery = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'sanctum']);
        $delivery->syncPermissions([
            'items.view',
            'dashboard.view',
            'pos.orders.view',
        ]);

        $this->command->info('✅ Roles & Permissions seeded: 43 permissions, 7 roles');
    }
}
