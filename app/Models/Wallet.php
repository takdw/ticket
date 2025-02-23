<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'integer',
    ];

    protected $fillable = ['amount', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
