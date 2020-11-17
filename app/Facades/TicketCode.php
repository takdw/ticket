<?php

namespace App\Facades;

use App\Core\Contracts\TicketCodeGenerator;
use Illuminate\Support\Facades\Facade;

class TicketCode extends Facade
{
    public static function getFacadeAccessor()
    {
        return TicketCodeGenerator::class;
    }
}