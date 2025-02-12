<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        return view('shop');
    }

    public function getProducts()
    {
        $path = storage_path('app/products.json');

        if (!file_exists($path)) {
            return response()->json(['message' => 'No products found'], 404);
        }

        $products = json_decode(file_get_contents($path), true);
        return response()->json($products);
    }

    public function featuredProducts()
    {
        $path = storage_path('app/products.json');

        if (!file_exists($path)) {
            return response()->json(['message' => 'No products found'], 404);
        }

        $products = json_decode(file_get_contents($path), true);
        return response()->json($products);
    }

}

