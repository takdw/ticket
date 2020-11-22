<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorMediaController extends Controller
{
    public function index()
    {
        $vendor = auth()->user();

        $keys = ['logo', 'image', 'license'];

        $base64Images = [];

        foreach ($keys as $key) {
            $attributeName = $key . '_path';

            try {
                $contents = Storage::get($vendor->{$attributeName});
                $extension = pathinfo($vendor->{$attributeName}, PATHINFO_EXTENSION);
                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        $type = 'jpeg';
                        break;
                    case 'png':
                        $type = 'png';
                        break;
                }

                $base64Images[$key]['filename'] = pathinfo($vendor->{$attributeName}, PATHINFO_BASENAME);
                $base64Images[$key]['preview'] = 'data:image/' . $type . ';base64,' . base64_encode($contents);
            } catch (\Exception $e) {
                $base64Images[$key] = '';
            }
        }

        return response()->json($base64Images, 200);
    }
}
