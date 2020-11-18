<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorsController extends Controller
{
    public function store()
    {
        request()->validate([
            'name' => 'required',
            'tin' => 'required',
        ]);

        $vendor = Vendor::create([
            'name' => request()->name,
            'tin' => request()->tin,
            'license_path' => request()->file('license')->storeAs('licenses', request()->file('license')->name),
            'logo_path' => request()->file('logo')->storeAs('logos', request()->file('logo')->name),
            'image_path' => request()->file('image')->storeAs('images', request()->file('image')->name),
        ]);

        return response()->json([], 201);
    }
}
