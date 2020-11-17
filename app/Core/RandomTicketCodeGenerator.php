<?php

namespace App\Core;

use App\Core\Contracts\TicketCodeGenerator;

class RandomTicketCodeGenerator implements TicketCodeGenerator
{
    public function generate()
    {
        return substr(str_shuffle(str_repeat('ABCDEFGHJLKMNPQRSTUVWXYZ23456789', 6)), 0, 6);
    }
}