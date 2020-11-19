<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    public function verify($user, Vendor $vendor)
    {
        return $user instanceof User && $user->isAdmin();
    }

    public function deactivate($user, Vendor $vendor)
    {
        return $user instanceof User && $user->isAdmin();
    }
}
