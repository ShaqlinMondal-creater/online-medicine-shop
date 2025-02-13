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

        // ✅ Define correct file path
        $filePath = storage_path('app/orders.json'); 

        // ✅ Check if orders.json exists and load existing orders
        if (file_exists($filePath)) {
            $orders = json_decode(file_get_contents($filePath), true);
        } else {
            $orders = [];
        }

        // ✅ Generate unique order ID (Auto-increment)
        $newOrderId = count($orders) > 0 ? end($orders)['order_id'] + 1 : 1;

        // ✅ Create order data
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
            'order_date' => Carbon::now()->toDateTimeString(),
        ];

        // ✅ Append new order to the array
        $orders[] = $orderData;

        // ✅ Try writing to orders.json and check if successful
        if (file_put_contents($filePath, json_encode($orders, JSON_PRETTY_PRINT)) === false) {
            return response()->json(['error' => 'Failed to write to orders.json'], 500);
        }

        // ✅ Redirect user to order-success page with order ID
        return response()->json([
            'message' => 'Order placed successfully!',
            'order_id' => $newOrderId,
            'redirect_url' => url("/order-success?order_id={$newOrderId}")
        ]);
    }
}
