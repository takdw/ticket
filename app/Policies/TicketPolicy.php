<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the vendor can publish the ticket.
     *
     * @param  \App\Models\Vendor  $vendor
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function publish(Vendor $vendor, Ticket $ticket)
    {
        return $ticket->vendor_id === $vendor->id;
    }
}
