<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
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
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function categories()       { return $this->hasMany(Category::class, 'created_by'); }
    public function items()            { return $this->hasMany(Item::class, 'created_by'); }
    public function customers()        { return $this->hasMany(Customer::class, 'created_by'); }
    public function restaurantTables() { return $this->hasMany(RestaurantTable::class, 'created_by'); }
    public function waiters()          { return $this->hasMany(Waiter::class, 'created_by'); }
    public function taxes()            { return $this->hasMany(Tax::class, 'created_by'); }
    public function restaurantSettings() { return $this->hasMany(RestaurantSettings::class, 'created_by'); }
    public function posOrders()        { return $this->hasMany(PosOrder::class, 'created_by'); }
    public function posSessions()      { return $this->hasMany(PosSession::class, 'user_id'); }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
