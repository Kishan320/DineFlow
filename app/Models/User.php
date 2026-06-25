<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseAuthenticatable as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Category;
use App\Models\Item;
use App\Models\Customer;
use App\Models\RestaurantTable;
use App\Models\Waiter;
use App\Models\Tax;
use App\Models\RestaurantSettings;
use App\Models\PosOrder;
use App\Models\PosSession;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'sanctum';

    /**
     * Existing relationships (unchanged).
     */
    public function categories()         { return $this->hasMany(Category::class, 'created_by'); }
    public function items()              { return $this->hasMany(Item::class, 'created_by'); }
    public function customers()          { return $this->hasMany(Customer::class, 'created_by'); }
    public function restaurantTables()   { return $this->hasMany(RestaurantTable::class, 'created_by'); }
    public function waiters()            { return $this->hasMany(Waiter::class, 'created_by'); }
    public function taxes()              { return $this->hasMany(Tax::class, 'created_by'); }
    public function restaurantSettings() { return $this->hasMany(RestaurantSettings::class, 'created_by'); }
    public function posOrders()          { return $this->hasMany(PosOrder::class, 'created_by'); }
    public function posSessions()        { return $this->hasMany(PosSession::class, 'user_id'); }

    /** Staff members under the same restaurant. */
    public function teamMembers()
    {
        return $this->hasMany(User::class, 'restaurant_id', 'restaurant_id');
    }

    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'restaurant_id',
    ];

    /**
     * Hidden from serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
