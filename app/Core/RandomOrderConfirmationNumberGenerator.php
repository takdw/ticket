<?php

namespace App\Core;

use App\Core\Contracts\TicketCodeGenerator;

class RandomOrderConfirmationNumberGenerator implements TicketCodeGenerator
{
    public function generate()
    {
        return substr(str_shuffle(str_repeat('ABCDEFGHJLKMNPQRSTUVWXYZ23456789', 28)), 0, 28);
    }
}