<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $path = storage_path('app/products.json');

        if (!file_exists($path)) {
            return abort(404, 'Product not found');
        }

        $products = json_decode(file_get_contents($path), true);
        $product = $products[$id] ?? null;

        if (!$product) {
            return abort(404, 'Product not found');
        }

        return view('product-details', compact('product'));
    }
}

