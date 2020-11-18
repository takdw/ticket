<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    public function publish($user, Ticket $ticket)
    {
        return $user instanceOf Vendor && $ticket->vendor_id == $user->id;
    }

    public function approve($user, Ticket $ticket)
    {
        return $user instanceOf User && $user->rolesList->contains('admin');
    }
}
