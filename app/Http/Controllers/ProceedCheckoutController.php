<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProceedCheckoutController extends Controller
{
    public function proceedCheckout(Request $request)
    {
        $cartFilePath = storage_path('app/cart.json');
        $productsFilePath = storage_path('app/products.json');
        $usersFilePath = storage_path('app/users.json');

        // ✅ Get `cart_id` and `user_id` from request (local storage)
        $cartId = $request->query('cart_id');
        $userId = $request->query('user_id');

        // ✅ Debugging Logs
        Log::info("🔍 Proceeding to checkout → Cart ID: {$cartId}, User ID: {$userId}");

        // ✅ Validate cart ID
        if (!$cartId) {
            return redirect('/cart')->with('error', '⚠️ No Cart ID found. Redirecting to cart...');
        }

        // ✅ Convert cart ID to integer (for strict matching)
        $cartId = is_numeric($cartId) ? (int)$cartId : null;

        // ✅ Load Cart Data
        $cartData = file_exists($cartFilePath) ? json_decode(file_get_contents($cartFilePath), true) : [];
        $userCart = collect($cartData)->firstWhere('cart_id', $cartId);

        if (!$userCart) {
            return redirect('/cart')->with('error', '⚠️ Cart not found. Redirecting to cart...');
        }

        // ✅ Load User Data
        $userData = file_exists($usersFilePath) ? json_decode(file_get_contents($usersFilePath), true) : [];
        $loggedInUser = collect($userData)->firstWhere('id', (int)$userCart['user_id']);

        // ✅ Load Product Data
        $productsData = file_exists($productsFilePath) ? json_decode(file_get_contents($productsFilePath), true) : [];

        // ✅ Merge product details into cart
        foreach ($userCart['products'] as &$cartProduct) {
            $productDetails = collect($productsData)->firstWhere('id', (int)$cartProduct['product_id']);
            if ($productDetails) {
                $cartProduct['name'] = $productDetails['name'] ?? 'Unknown Product';
                $cartProduct['sku'] = $productDetails['sku'] ?? 'N/A';
                $cartProduct['image'] = $productDetails['image'] ?? 'default.jpg';
                $cartProduct['category'] = $productDetails['category'] ?? 'Unknown';
                $cartProduct['brand'] = $productDetails['brand'] ?? 'Unknown';
            }
        }

        Log::info("✅ Checkout Allowed: Cart ID = " . json_encode($cartId));

        return view('proceed-checkout', compact('userCart', 'loggedInUser'));
    }
}
