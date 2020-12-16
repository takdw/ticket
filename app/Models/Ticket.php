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
        'published_at',
        'poster',
    ];

    protected $casts = [
        'price' => 'integer',
        'date' => 'datetime',
        'vendor_id' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function digitalTickets()
    {
        return $this->hasMany(DigitalTicket::class);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('published_at')
                    ->whereNotNull('approved_at');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }

    public function isApproved()
    {
        return !(is_null($this->published_at) || is_null($this->approved_at)); 
    }
}
