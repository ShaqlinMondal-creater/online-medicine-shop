<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        // Validate request data
        $request->validate([
            'user_id' => 'required',
            'cart_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'address' => 'required',
            'shipping' => 'required',
            'payment' => 'required',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'shipping_fee' => 'required|numeric',
            'payment_fee' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'products' => 'required|array',
        ]);
    
        // Define file paths
        $orderFilePath = storage_path('app/orders.json');
        $cartFilePath = storage_path('app/cart.json');
    
        // Load existing orders
        if (file_exists($orderFilePath)) {
            $orders = json_decode(file_get_contents($orderFilePath), true);
        } else {
            $orders = [];
        }
    
        // ✅ Generate Order ID in format: DDMMYYYY + Increment
        $today = date('dmY'); // Example: 1322025 (13-Feb-2025)
        $latestOrderId = 0;
    
        if (!empty($orders)) {
            // Get last order ID and check if it matches today's format
            $lastOrder = end($orders);
            $lastOrderId = $lastOrder['order_id'];
    
            if (strpos($lastOrderId, $today) === 0) {
                $latestOrderId = intval(substr($lastOrderId, 8)) + 1; // Extract last digits and increment
            } else {
                $latestOrderId = 1; // Start fresh for a new day
            }
        } else {
            $latestOrderId = 1;
        }
    
        // Final Order ID
        $newOrderId = $today . $latestOrderId;
    
        // Create order data
        $orderData = [
            'order_id' => $newOrderId,
            'user_id' => $request->user_id,
            'cart_id' => $request->cart_id,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'shipping' => $request->shipping,
            'payment' => $request->payment,
            'subtotal' => number_format($request->subtotal, 2),
            'tax' => number_format($request->tax, 2),
            'shipping_fee' => number_format($request->shipping_fee, 2),
            'payment_fee' => number_format($request->payment_fee, 2),
            'grand_total' => number_format($request->grand_total, 2),
            'products' => $request->products,
            'order_date' => now()->toDateTimeString(),
        ];
    
        // Append new order to the array
        $orders[] = $orderData;
    
        // ✅ Save updated orders to orders.json
        if (file_put_contents($orderFilePath, json_encode($orders, JSON_PRETTY_PRINT)) === false) {
            return response()->json(['error' => 'Failed to write to orders.json'], 500);
        }
    
        // ✅ Load existing carts and delete the one with cart_id
        if (file_exists($cartFilePath)) {
            $carts = json_decode(file_get_contents($cartFilePath), true);
    
            // Filter out the cart that matches cart_id
            $updatedCarts = array_filter($carts, function ($cart) use ($request) {
                return intval($cart['cart_id']) !== intval($request->cart_id);
            });
    
            // ✅ Save the updated carts back to cart.json (overwrite with only remaining carts)
            file_put_contents($cartFilePath, json_encode(array_values($updatedCarts), JSON_PRETTY_PRINT));
        }
    
        // ✅ Store order details in session for order-success page
        session(['order_details' => $orderData]);
    
        return response()->json([
            'message' => 'Order placed successfully!',
            'order_id' => $newOrderId,
            'redirect_url' => url("/order-success")
        ]);
    }
    
}
