<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorsController extends Controller
{
    public function store()
    {
        request()->validate([
            'name' => 'required',
            'tin' => 'required',
            'password' => 'required|confirmed',
        ]);

        $vendor = Vendor::create([
            'name' => request()->name,
            'tin' => request()->tin,
            'license_path' => request()->license->storeAs('licenses', request()->license->getClientOriginalName()),
            'logo_path' => request()->logo->storeAs('logos', request()->logo->getClientOriginalName()),
            'image_path' => request()->image->storeAs('images', request()->image->getClientOriginalName()),
            'password' => Hash::make(request()->password),
        ]);

        return response()->json($vendor, 201);
    }

    public function update()
    {
        $validated = request()->validate([
            'old_password' => [
                'required_with:new_password',
                function ($attribute, $value, $fail) {
                    if (! Hash::check($value, auth()->user()->password)) {
                        $fail("Invalid data provided.");
                    }
                },
            ],
            'new_password' => ['required_with:old_password', 'confirmed'],
            'name' => ['sometimes', 'required'],
            'phone_number' => ['sometimes', 'required'],
            'logo' => ['sometimes', 'required'],
            'image' => ['sometimes', 'required'],
            'license' => ['sometimes', 'required'],
        ]);

        $vendor = auth()->user();

        if (!($vendor instanceof Vendor)) {
            if (!($vendor instanceof User || $vendor->rolesList->contains('admin'))) {
                abort(403, 'You are unauthorized for this action.');
            }
            $vendor = Vendor::findOrFail(request()->vendor_id);
        }

        $allowedUpdates = ['name', 'new_password', 'logo', 'image', 'license'];

        foreach ($validated as $key => $value) {
            if (array_search($key, $allowedUpdates) !== false) {
                if ($key == 'new_password') {
                    $vendor->password = Hash::make($value);
                } else if (array_search($key, ['logo', 'image', 'license']) !== false) {
                    $vendor->{$key.'_path'} = request()->{$key}->storeAs($key.'s', request()->{$key}->getClientOriginalName());
                } else {
                    $vendor->{$key} = $value;
                }
            }
        }

        if ($vendor->isDirty()) {
            $vendor->save();
        }

        return response()->json([], 200);
    }
}
