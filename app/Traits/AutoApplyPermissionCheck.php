<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use App\Models\User;
use App\Models\PosOrder;
use App\Models\PosKot;
use App\Models\Customer;
use App\Models\RestaurantTable;

trait AutoApplyPermissionCheck
{
    /**
     * Apply permission scope to the query based on user's role and ownership
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $module The module/table name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyPermissionScope($query, $module)
    {
        // Skip permission check if no authenticated user (e.g., in console commands)
        if (!auth()->check()) {
            return $query;
        }

        $user = auth()->user();

        // 1. Super Admin: full access
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $modelClass = get_class($query->getModel());

        // 2. Customer
        if ($user->hasRole('Customer')) {
            if ($modelClass === PosOrder::class) {
                // Own Orders
                return $query->whereHas('customer', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
            if ($modelClass === Customer::class) {
                // Own Profile / Addresses
                return $query->where('user_id', $user->id);
            }
            if (Schema::hasColumn($module, 'created_by')) {
                return $query->where('created_by', $user->id);
            }
            return $query;
        }

        // 3. Waiter
        if ($user->hasRole('Waiter')) {
            if ($modelClass === PosOrder::class) {
                // Assigned Orders (waiter_id matches user_id or created by them)
                return $query->where(function ($q) use ($user) {
                    $q->where('waiter_id', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            }
            if ($modelClass === PosKot::class) {
                // Assigned KOTs (through assigned orders)
                return $query->whereHas('posOrder', function ($q) use ($user) {
                    $q->where('waiter_id', $user->id)
                      ->orWhere('created_by', $user->id);
                });
            }
            // For tables and other entities, they should see data from their restaurant
            if ($user->restaurant_id && Schema::hasColumn($module, 'created_by')) {
                return $query->whereIn('created_by', User::where('restaurant_id', $user->restaurant_id)->pluck('id'));
            }
            if (Schema::hasColumn($module, 'created_by')) {
                return $query->where('created_by', $user->id);
            }
            return $query;
        }

        // 4. Delivery Staff
        if ($user->hasRole('Delivery Staff')) {
            if ($modelClass === PosOrder::class) {
                // Assigned Deliveries/Orders
                return $query->where('created_by', $user->id)
                             ->where('order_type', 'Delivery');
            }
            if (Schema::hasColumn($module, 'created_by')) {
                return $query->where('created_by', $user->id);
            }
            return $query;
        }

        // 5. Kitchen Staff, Cashier, Manager, Admin -> Restaurant Scoped
        if ($user->restaurant_id) {
            if (Schema::hasColumn($module, 'created_by')) {
                return $query->whereIn('created_by', User::where('restaurant_id', $user->restaurant_id)->pluck('id'));
            }
        } else {
            // Fallback for Users without restaurant_id
            if (Schema::hasColumn($module, 'created_by')) {
                return $query->where('created_by', $user->id);
            }
        }

        return $query;
    }

    /**
     * Boot the trait to automatically set created_by
     */
    protected static function bootAutoApplyPermissionCheck(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && empty($model->created_by)) {
                $tableName = $model->getTable();
                if (Schema::hasColumn($tableName, 'created_by')) {
                    $model->created_by = auth()->id();
                }
            }
        });
    }
}
