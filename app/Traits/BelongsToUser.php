<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToUser
{
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope records to the authenticated user's restaurant.
     *
     * If the user has a restaurant_id, we scope to ALL records belonging
     * to any user in that restaurant (enabling team data sharing).
     * Falls back to personal scoping if restaurant_id is not set.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        $user = \App\Models\User::find($userId);

        // If the user belongs to a restaurant, show all restaurant data
        if ($user && $user->restaurant_id) {
            $restaurantId = $user->restaurant_id;

            return $query->whereIn(
                $this->getTable() . '.created_by',
                \App\Models\User::where('restaurant_id', $restaurantId)->pluck('id')
            );
        }

        // Fallback: personal scoping (legacy / no restaurant assigned)
        return $query->where($this->getTable() . '.created_by', $userId);
    }

    protected static function bootBelongsToUser(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });
    }
}
