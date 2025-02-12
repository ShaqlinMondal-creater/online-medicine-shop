<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getBrands()
    {
        $path = storage_path('app/brands.json');

        if (!file_exists($path)) {
            return response()->json(['message' => 'No brands found'], 404);
        }

        $brands = json_decode(file_get_contents($path), true);
        return response()->json($brands);
    }
}
