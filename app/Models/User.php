<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'gender',
        'address',
        'role',
        'status',
        'is_online'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user', 'user_id', 'coupon_id')
            ->withTimestamps();
    }
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission($key)
    {
        return $this->permissions->contains('key', $key);
    }
}
