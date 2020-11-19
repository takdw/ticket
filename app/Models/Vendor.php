<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends User
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'tin',
        'image_path',
        'logo_path',
        'license_path',
        'password',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
