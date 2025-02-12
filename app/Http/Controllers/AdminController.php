<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private $userFile = 'users.json';
    private $productFile = 'products.json';
    private $orderFile = 'orders.json';

    // ✅ Function to Load JSON Data
    private function loadData($file)
    {
        $path = storage_path("app/{$file}");
        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }

    // ✅ Fetch Total Counts for Admin Dashboard
    public function getStats()
    {
        $users = $this->loadData($this->userFile);
        $products = $this->loadData($this->productFile);
        $orders = $this->loadData($this->orderFile);

        return response()->json([
            'users' => count($users),
            'products' => count($products),
            'orders' => count($orders),
        ]);
    }

    public function showAllData(Request $request)
    {
        $type = $request->query('type', 'users'); // Default to users
        $filePath = match ($type) {
            'products' => 'products.json',
            'orders' => 'orders.json',
            default => 'users.json',
        };

        // Load JSON data
        $data = [];
        $path = storage_path("app/{$filePath}");
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
        }

        return view('admin.all-data', compact('data', 'type'));
    }

    public function getProducts()
    {
        return $this->getDataFromJson('products.json');
    }

    public function updateProduct(Request $request)
    {
        $index = $request->query('index');
        $path = storage_path("app/products.json");

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $data = json_decode(file_get_contents($path), true);

        if (!isset($data[$index])) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // ✅ Update product details
        $data[$index]['name'] = $request->input('name');
        $data[$index]['price'] = $request->input('price');
        $data[$index]['stock'] = $request->input('stock');

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Product updated successfully']);
    }

    public function deleteProduct(Request $request)
    {
        $index = $request->query('index');
        $path = storage_path("app/products.json");

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $data = json_decode(file_get_contents($path), true);

        if (!isset($data[$index])) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        array_splice($data, $index, 1);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // ✅ Helper function to read JSON data
    private function getDataFromJson($file)
    {
        $path = storage_path("app/{$file}");
        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $data = json_decode(file_get_contents($path), true);
        return response()->json($data);
    }

    


}
