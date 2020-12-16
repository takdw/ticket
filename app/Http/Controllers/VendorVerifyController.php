<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorVerifyController extends Controller
{
    public function store(Vendor $vendor)
    {
        $this->authorize('verify', $vendor);

        $vendor->verified_at = now();
        $vendor->save();

        return response()->json([], 200);
    }

    public function destory(Vendor $vendor)
    {
        $this->authorize('verify', $vendor);
        
        $vendor->verified_at = null;
        $vendor->save();

        return response()->json([], 200);
    }
}
