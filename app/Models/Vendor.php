<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Vendor extends User
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tin',
        'image_path',
        'logo_path',
        'license_path',
    ];
}
