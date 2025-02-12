<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories()
    {
        $path = storage_path('app/categories.json');

        if (!file_exists($path)) {
            return response()->json(['message' => 'No categories found'], 404);
        }

        $categories = json_decode(file_get_contents($path), true);
        return response()->json($categories);
    }
}

