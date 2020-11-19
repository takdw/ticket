<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function deactivate($user)
    {
        return $user instanceof User && $user->isAdmin();
    }
}
