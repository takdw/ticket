<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'country',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['wallet_balance', 'roles_list'];

    protected static function booted()
    {
        static::created(function ($user) {
            if (is_null($user->wallet)) {
                $user->wallet()->create(['amount' => 0]);
            }
        });
    }

    public function digitalTickets()
    {
        return $this->hasManyThrough(DigitalTicket::class, Order::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getRolesListAttribute()
    {
        return $this->roles->pluck('name')->values();
    }

    public function isAdmin()
    {
        return $this->rolesList->contains('admin');
    }

    public function getWalletBalanceAttribute()
    {
        return $this->wallet->amount;
    }
}
