<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorActivationController extends Controller
{
    public function store(Vendor $vendor)
    {
        $this->authorize('deactivate', $vendor);
        
        $vendor->deactivated_at = now();
        $vendor->save();

        return response()->json([], 200);
    }

    public function destory(Vendor $vendor)
    {
        $this->authorize('deactivate', $vendor);
        
        $vendor->deactivated_at = null;
        $vendor->save();

        return response()->json([], 200);
    }
}
