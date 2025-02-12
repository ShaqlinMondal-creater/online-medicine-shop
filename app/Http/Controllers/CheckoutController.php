<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    public function showCheckout()
    {
        $authFilePath = storage_path('app/auth.json');
        $cartFilePath = storage_path('app/cart.json');
        $productsFilePath = storage_path('app/products.json');

        // ✅ Load authenticated user data
        if (file_exists($authFilePath)) {
            $authData = json_decode(file_get_contents($authFilePath), true);
            $loggedInUser = collect($authData)->firstWhere('auth_id', 'true');
        } else {
            return redirect('/login')->with('error', 'You need to log in first.');
        }

        // ✅ Redirect if no user is found
        if (!$loggedInUser) {
            return redirect('/login')->with('error', 'You need to log in first.');
        }

        // ✅ Load cart data
        $cartData = file_exists($cartFilePath) ? json_decode(file_get_contents($cartFilePath), true) : [];
        $userCart = collect($cartData)->firstWhere('user_id', $loggedInUser['user_id']);

        // ✅ Load products data
        $productsData = file_exists($productsFilePath) ? json_decode(file_get_contents($productsFilePath), true) : [];

        // ✅ Merge product details into cart
        if ($userCart && isset($userCart['products'])) {
            foreach ($userCart['products'] as &$cartProduct) {
                $productDetails = collect($productsData)->firstWhere('id', $cartProduct['product_id']);
                if ($productDetails) {
                    $cartProduct['name'] = $productDetails['name'] ?? 'Unknown Product';
                    $cartProduct['sku'] = $productDetails['sku'] ?? 'N/A';
                    $cartProduct['image'] = $productDetails['image'] ?? 'default.jpg';
                }
            }
        }

        return view('checkout', compact('loggedInUser', 'userCart'));
    }

//     public function placeOrder(Request $request)
// {
//     // ✅ Debugging: Log received request data
//     \Log::info('Received Order Request:', $request->all());

//     // ✅ Validate Required Fields
//     $validated = $request->validate([
//         'name' => 'required|string',
//         'email' => 'required|string|email',
//         'mobile' => 'required|string',
//         'address' => 'required|string',
//         'shipping' => 'required|string',
//         'shipping_fee' => 'required|numeric',
//         'payment' => 'required|string',
//         'payment_fee' => 'required|numeric',
//         'total' => 'required|numeric'
//     ]);

//     if (!$validated) {
//         return response()->json(['message' => 'Missing required fields!'], 400);
//     }

//     // ✅ File Paths
//     $authFilePath = storage_path('app/auth.json');
//     $addressFilePath = storage_path('app/address.json');
//     $ordersFilePath = storage_path('app/orders.json');
//     $cartFilePath = storage_path('app/cart.json');

//     // ✅ Get Logged-in User from auth.json
//     $authData = file_exists($authFilePath) ? json_decode(file_get_contents($authFilePath), true) : [];
//     $loggedInUser = collect($authData)->firstWhere('auth_id', 'true');

//     if (!$loggedInUser) {
//         return response()->json(['message' => 'User not logged in!'], 400);
//     }

//     $userId = $loggedInUser['user']['id'];

//     // ✅ Load Address Data
//     $addressData = file_exists($addressFilePath) ? json_decode(file_get_contents($addressFilePath), true) : [];
//     $userAddress = collect($addressData)->firstWhere('user_id', $userId);

//     // ✅ Insert Address and Get Address ID
//     if ($userAddress) {
//         $newAddressId = count($userAddress['address']) + 1;
//         $userAddress['address'][] = [
//             "address_id" => $newAddressId,
//             "details" => $request->address
//         ];
//     } else {
//         $newAddressId = 1;
//         $addressData[] = [
//             "user_id" => $userId,
//             "address" => [
//                 [
//                     "address_id" => $newAddressId,
//                     "details" => $request->address
//                 ]
//             ]
//         ];
//     }

//     file_put_contents($addressFilePath, json_encode($addressData, JSON_PRETTY_PRINT));

//     // ✅ Load Orders Data
//     $ordersData = file_exists($ordersFilePath) ? json_decode(file_get_contents($ordersFilePath), true) : [];
//     $lastOrder = end($ordersData);
//     $newOrderId = 202500 + (isset($lastOrder['order_id']) ? intval(substr($lastOrder['order_id'], 4)) + 1 : 1);

//     // ✅ Load Cart Data
//     $cartData = file_exists($cartFilePath) ? json_decode(file_get_contents($cartFilePath), true) : [];
//     $userCart = collect($cartData)->firstWhere('user_id', $userId);

//     if (!$userCart) {
//         return response()->json(['message' => 'Cart is empty!'], 400);
//     }

//     // ✅ Create New Order Entry
//     $newOrder = [
//         "order_id" => $newOrderId,
//         "user_id" => $userId,
//         "user_email" => $request->email,
//         "user_mobile" => $request->mobile,
//         "address_id" => $newAddressId,
//         "cart_id" => $userCart['cart_id'],
//         "subtotal" => $userCart['Total'],
//         "Total" => $request->total,
//         "Payment" => [
//             [
//                 "name" => ucfirst(str_replace('_', ' ', $request->payment)),
//                 "fee" => $request->payment_fee
//             ]
//         ],
//         "shipping" => [
//             [
//                 "name" => ucfirst(str_replace('_', ' ', $request->shipping)),
//                 "fee" => $request->shipping_fee
//             ]
//         ],
//         "date" => now()->format('Y-m-d H:i:s')
//     ];

//     // ✅ Save Order Data
//     $ordersData[] = $newOrder;
//     file_put_contents($ordersFilePath, json_encode($ordersData, JSON_PRETTY_PRINT));

//     // ✅ Clear User's Cart
//     $cartData = array_filter($cartData, fn($cart) => $cart['user_id'] !== $userId);
//     file_put_contents($cartFilePath, json_encode(array_values($cartData), JSON_PRETTY_PRINT));

//     return response()->json(['message' => 'Order placed successfully!', 'order_id' => $newOrderId]);
// }

public function placeOrder(Request $request)
{
    \Log::info('Received Raw Request:', [$request->getContent()]); // ✅ Log full raw request

    // ✅ Decode JSON manually
    $inputData = json_decode($request->getContent(), true);

    if (!$inputData) {
        \Log::error('Invalid JSON Data Received');
        return response()->json(['message' => 'Invalid JSON data received!'], 400);
    }

    \Log::info('Parsed Order Data:', $inputData); // ✅ Log parsed JSON data

    // ✅ Validate Required Fields
    $validator = Validator::make($inputData, [
        'name' => 'required|string',
        'email' => 'required|string|email',
        'mobile' => 'required|string',
        'address' => 'required|string',
        'shipping' => 'required|string',
        'shipping_fee' => 'required|numeric',
        'payment' => 'required|string',
        'payment_fee' => 'required|numeric',
        'total' => 'required|numeric'
    ]);

    if ($validator->fails()) {
        \Log::error('Validation Failed:', $validator->errors()->toArray());
        return response()->json([
            'message' => 'Validation failed!',
            'errors' => $validator->errors()
        ], 400);
    }

    return response()->json(['message' => 'Validation Passed!'], 200);
}








}

