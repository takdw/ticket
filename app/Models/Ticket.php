<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'date',
        'venue',
        'city',
        'price',
        'additional_info',
    ];

    protected $casts = [
        'price' => 'integer',
        'date' => 'datetime',
    ];
}
