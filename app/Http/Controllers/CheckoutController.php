<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function showCheckout(Request $request)
    {
        $cartFilePath = storage_path('app/cart.json');
        $authFilePath = storage_path('app/auth.json');
    
        // ✅ Get `user_id` & `cart_id` from headers
        $userId = $request->header('X-User-ID');
        $cartId = $request->header('X-Cart-ID');
    
        // ✅ Debugging logs
        \Log::info("Checking user session: User ID = $userId, Cart ID = $cartId");
    
        if (!$userId || !$cartId) {
            return redirect('/login')->with('error', 'You need to log in first.');
        }
    
        // ✅ Load all cart data
        $cartData = file_exists($cartFilePath) ? json_decode(file_get_contents($cartFilePath), true) : [];
    
        // ✅ Find correct cart using `cart_id`
        $userCart = collect($cartData)->firstWhere('cart_id', (int) $cartId);
    
        // ✅ If cart is missing, prevent infinite redirection loops
        if (!$userCart) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }
    
        return view('checkout', compact('userCart'));
    }
    
    
}
