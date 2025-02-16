<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $orderFilePath = storage_path('app/orders.json');
        $cartFilePath = storage_path('app/cart.json');

        // ✅ Validate incoming request
        $request->validate([
            'user_id' => 'required|string',
            'cart_id' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'shipping' => 'required|string',
            'payment' => 'required|string',
            'subtotal' => 'required|numeric',
            'gst' => 'required|numeric',
            'shipping_fee' => 'required|numeric',
            'payment_fee' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'products' => 'required|array|min:1',
        ]);

        // ✅ Load existing orders from JSON file
        $orders = file_exists($orderFilePath) ? json_decode(file_get_contents($orderFilePath), true) : [];

        // ✅ Generate a unique order ID (auto-increment)
        $orderId = count($orders) > 0 ? max(array_column($orders, 'order_id')) + 1 : 1;

        // ✅ Construct new order data
        $newOrder = [
            "order_id" => $orderId,
            "user_id" => $request->user_id,
            "cart_id" => $request->cart_id,
            "name" => $request->name,
            "email" => $request->email,
            "mobile" => $request->mobile,
            "address" => $request->address,
            "shipping_method" => $request->shipping,
            "payment_method" => $request->payment,
            "subtotal" => (float) $request->subtotal,
            "gst" => (float) $request->gst,
            "shipping_fee" => (float) $request->shipping_fee,
            "payment_fee" => (float) $request->payment_fee,
            "grand_total" => (float) $request->grand_total,
            "products" => $request->products,
            "status" => "pending",
            "date" => now()->toDateTimeString(),
        ];

        // ✅ Store the new order in `orders.json`
        $orders[] = $newOrder;
        file_put_contents($orderFilePath, json_encode($orders, JSON_PRETTY_PRINT));

        // ✅ Remove cart entry after placing order
        $cartData = file_exists($cartFilePath) ? json_decode(file_get_contents($cartFilePath), true) : [];
        $updatedCart = array_filter($cartData, fn($cart) => $cart['cart_id'] != $request->cart_id);
        file_put_contents($cartFilePath, json_encode(array_values($updatedCart), JSON_PRETTY_PRINT));

        // ✅ Store Order ID in local storage for 5 minutes
        $orderExpiryTime = time() + (5 * 60); // 5 minutes from now

        return response()->json([
            "message" => "Order placed successfully!",
            "order_id" => $orderId,
            "order_expiry" => $orderExpiryTime, // Send expiry time to store in local storage
            "redirect_url" => route('order.success'),
        ]);
    }

    public function getOrder(Request $request)
    {
        $orderFilePath = storage_path('app/orders.json');

        $orderId = $request->query('order_id');

        if (!$orderId) {
            return response()->json(["error" => "Order ID missing!"], 400);
        }

        $orders = file_exists($orderFilePath) ? json_decode(file_get_contents($orderFilePath), true) : [];

        $order = collect($orders)->firstWhere('order_id', (int) $orderId);

        if (!$order) {
            return response()->json(["error" => "Order not found!"], 404);
        }

        return response()->json($order);
    }

}
