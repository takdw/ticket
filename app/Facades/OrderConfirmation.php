<?php

namespace App\Facades;

use App\Core\Contracts\OrderConfirmationNumberGenerator;
use Illuminate\Support\Facades\Facade;

class OrderConfirmation extends Facade
{
    public static function getFacadeAccessor()
    {
        return OrderConfirmationNumberGenerator::class;
    }
}