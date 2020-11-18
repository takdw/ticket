<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorVerifyController extends Controller
{
    public function store(Vendor $vendor)
    {
        $this->authorize('verify', $vendor);

        $vendor->approved_at = now();
        $vendor->save();

        return response()->json([], 200);
    }
}
