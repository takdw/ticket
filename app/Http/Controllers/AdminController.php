<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:administer']);
    }

    public function getTickets()
    {
        $tickets = Ticket::query();

        $allowedFilters = ['approved', 'published', 'unapproved', 'unpublished'];

        $status = request()->status;

        if ($status && array_search($status, $allowedFilters) !== false) {
            switch($status) {
                case 'approved':
                    $tickets->whereNotNull('approved_at');
                    break;
                case 'published':
                    $tickets->whereNotNull('published_at');
                    break;
                case 'unapproved':
                    $tickets->whereNull('approved_at');
                    break;
                case 'unpublished':
                    $tickets->whereNull('published_at');
                    break;
            }
        }

        return response()->json(
            $tickets->with('vendor')
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(), 
            200
        );
    }

    public function getUsers()
    {
        $users = User::query();

        $allowedFilters = ['active', 'inactive'];

        $status = request()->status;

        if ($status && array_search($status, $allowedFilters) !== false) {
            switch($status) {
                case 'active':
                    $users->whereNull('deactivated_at');
                    break;
                case 'inactive':
                    $users->whereNotNull('deactivated_at');
                    break;
            }
        }

        return response()->json(
            $users->latest()
                    ->paginate(10)
                    ->withQueryString(), 
            200
        );
    }
}
