<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private $cartFile = 'cart.json';
    private $productFile = 'products.json';

    // Load cart data
    private function getCartData()
    {
        $filePath = storage_path('app/cart.json');

        if (!file_exists($filePath)) {
            \Log::error("Cart file not found: " . $filePath);
            return [];
        }

        $data = file_get_contents($filePath);

        // Handle empty JSON file
        if (empty($data)) {
            \Log::warning("Cart file is empty, returning empty array.");
            return [];
        }

        return json_decode($data, true) ?? [];
    }

    public function getCart()
    {
        $cart = $this->getCartData(); // ✅ Get cart data
        $products = $this->getProductData(); // ✅ Fetch product details from products.json
        $userId = $this->getUserId();

        // ✅ Fetch only the logged-in user's cart
        $userCart = collect($cart)->firstWhere('user_id', $userId);

        if (!$userCart) {
            return response()->json([]); // ✅ Return empty array if no cart exists for this user
        }

        // ✅ Convert `products` from an object to an array
        $userCart['products'] = array_values((array)$userCart['products']);

        // ✅ Fetch `name`, `image`, and `sku` from `products.json`
        foreach ($userCart['products'] as &$product) {
            $productDetails = collect($products)->firstWhere('id', $product['product_id']);

            if ($productDetails) {
                $product['name'] = $productDetails['name'];
                $product['image'] = $productDetails['image'];
                $product['sku'] = $productDetails['sku'];
            } else {
                // ✅ Fallback values for missing products
                $product['name'] = "Unknown Product";
                $product['image'] = "default.jpg";
                $product['sku'] = "N/A";
            }
        }

        return response()->json([$userCart]);
    }  

    // Load product data
    private function getProductData()
    {
        $filePath = storage_path('app/products.json'); // Correct Path

        if (!file_exists($filePath)) {
            \Log::error("Product file not found: " . $filePath);
            return [];
        }

        $data = file_get_contents($filePath);
        \Log::info("Product JSON Data: " . $data); // Debugging

        return json_decode($data, true);
    }

    private function getUserId()
    {
        session_start(); // Ensure the session starts

        $usersFilePath = storage_path('app/users.json');

        if (!file_exists($usersFilePath)) {
            \Log::error("Users file not found: " . $usersFilePath);
            return 'guest_' . session_id(); // Return guest session ID if users.json is missing
        }

        $users = json_decode(file_get_contents($usersFilePath), true);

        // DEBUG: Log session data
        \Log::info("Session Data: " . json_encode($_SESSION));

        // Check if user session exists
        if (!empty($_SESSION['email'])) {
            $userEmail = $_SESSION['email'];

            // Find user in users.json
            foreach ($users as $user) {
                if ($user['email'] === $userEmail) {
                    if ($user['role'] === 'admin') {
                        \Log::info("Admin detected, skipping cart creation.");
                        return null; // Ignore admin users
                    }

                    \Log::info("User found in users.json: " . json_encode($user));
                    return (string) $user['id']; // Return user ID as a string
                }
            }

            \Log::error("User not found in users.json: " . $userEmail);
            return 'guest_' . session_id(); // If user not found, treat as guest
        }

        // No session found, treat as guest
        $guestId = 'guest_' . session_id();
        \Log::info("Guest User ID: " . $guestId);
        return $guestId;
    }

    // Add product to cart
    public function addToCart($id)
    {
        try {
            // Load product and cart data
            $products = $this->getProductData();
            $cart = $this->getCartData() ?? []; // Ensure cart is always an array
            \Log::info("Received Product ID for Cart: " . $id);

            // Ensure product ID is an integer
            $id = (int) $id;

            // Find product
            $product = collect($products)->firstWhere('id', $id);
            if (!$product) {
                \Log::error("Product not found in JSON: ID " . $id);
                return response()->json(['error' => 'Product not found!'], 404);
            }

            // Get user ID (logged-in or guest) - ignore admin users
            $userId = $this->getUserId();
            if (!$userId) {
                return response()->json(['error' => 'Admins cannot add to cart.'], 403);
            }

            // ✅ Generate a unique cart_id
            $existingCartIds = array_column($cart, 'cart_id'); // Get all existing cart IDs
            $newCartId = empty($existingCartIds) ? 1 : (max($existingCartIds) + 1);

            // ✅ Check if the user already has a cart
            $existingCartIndex = collect($cart)->search(fn($item) => $item['user_id'] == $userId);

            if ($existingCartIndex !== false) {
                // ✅ User already has a cart, add product to it
                $existingCart = &$cart[$existingCartIndex];

                $productExists = collect($existingCart['products'])->firstWhere('product_id', $id);

                if ($productExists) {
                    // ✅ Update quantity if product exists
                    foreach ($existingCart['products'] as &$prod) {
                        if ($prod['product_id'] == $id) {
                            $prod['quantity'] += 1;
                        }
                    }
                } else {
                    // ✅ Add new product to user's cart
                    $existingCart['products'][] = [
                        "product_id" => $product['id'],
                        "price" => $product['price'],
                        "quantity" => 1
                    ];
                }

                // ✅ Recalculate Total Price
                $existingCart['Total'] = array_reduce($existingCart['products'], fn($sum, $prod) => $sum + ($prod['price'] * $prod['quantity']), 0);
            } else {
                // ✅ User has no existing cart, create a new entry with unique `cart_id`
                $cart[] = [
                    "cart_id" => $newCartId,
                    "user_id" => $userId,
                    "products" => [
                        [
                            "product_id" => $product['id'],
                            "price" => $product['price'],
                            "quantity" => 1
                        ]
                    ],
                    "Total" => $product['price'],
                    "date" => date('Y-m-d H:i:s') // Add date when cart is created
                ];
            }

            // ✅ Save to cart.json in correct format
            $cartFilePath = storage_path('app/cart.json');
            file_put_contents($cartFilePath, json_encode($cart, JSON_PRETTY_PRINT));

            \Log::info("Cart Updated Successfully: " . json_encode($cart));

            return response()->json(['message' => 'Product added to cart successfully!']);
        } catch (\Exception $e) {
            \Log::error("Cart Error: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    // Show cart page
    public function cart()
    {
        $cart = $this->getCartData();
        $userId = Auth::check() ? Auth::id() : 'guest_' . session()->getId();

        return view('cart', ['cartItems' => $cart[$userId] ?? []]);
    }

    public function removeFromCart($productId)
    {
        try {
            $cart = $this->getCartData();
            $userId = $this->getUserId();
            $updatedCart = [];

            $found = false; // ✅ Track if product was found

            foreach ($cart as $key => &$userCart) {
                if ($userCart['user_id'] == $userId) {
                    // ✅ Filter out the product to remove
                    $userCart['products'] = array_values(array_filter($userCart['products'], function ($product) use ($productId, &$found) {
                        if ($product['product_id'] == $productId) {
                            $found = true;
                            return false;  // ✅ Remove this product
                        }
                        return true;
                    }));

                    // ✅ If products are empty, keep the cart but set it to empty
                    if (empty($userCart['products'])) {
                        $userCart['products'] = []; // ✅ Maintain empty cart instead of deleting it
                        $userCart['Total'] = 0;
                    } else {
                        // ✅ Recalculate total price if products exist
                        $userCart['Total'] = array_reduce($userCart['products'], fn($sum, $prod) => $sum + ($prod['price'] * $prod['quantity']), 0);
                    }

                    $updatedCart[] = $userCart; // ✅ Save updated cart
                } else {
                    $updatedCart[] = $userCart; // ✅ Keep other users' carts unchanged
                }
            }

            // ✅ Save updated cart
            $cartFilePath = storage_path('app/cart.json');
            file_put_contents($cartFilePath, json_encode($updatedCart, JSON_PRETTY_PRINT));

            return response()->json(['message' => $found ? 'Item removed from cart successfully!' : 'Product not found in cart!']);
        } catch (\Exception $e) {
            \Log::error("Error removing item from cart: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }



    public function updateCart($productId, $action)
    {
        try {
            $cart = $this->getCartData();
            $userId = $this->getUserId();
            $updatedCart = [];

            $found = false; // ✅ Track if product was found

            foreach ($cart as $key => &$userCart) {
                if ($userCart['user_id'] == $userId) {
                    foreach ($userCart['products'] as &$product) {
                        if ($product['product_id'] == $productId) {
                            $found = true;
                            if ($action === "increase") {
                                $product['quantity'] += 1;
                            } elseif ($action === "decrease" && $product['quantity'] > 1) {
                                $product['quantity'] -= 1;
                            }
                        }
                    }

                    // ✅ Recalculate total price
                    $userCart['Total'] = array_reduce($userCart['products'], fn($sum, $prod) => $sum + ($prod['price'] * $prod['quantity']), 0);

                    $updatedCart[] = $userCart;
                } else {
                    $updatedCart[] = $userCart;
                }
            }

            // ✅ Save updated cart
            $cartFilePath = storage_path('app/cart.json');
            file_put_contents($cartFilePath, json_encode(array_values($updatedCart), JSON_PRETTY_PRINT));

            if ($found) {
                return response()->json(['message' => 'Cart updated successfully!']);
            } else {
                return response()->json(['message' => 'Product not found in cart!'], 404);
            }

        } catch (\Exception $e) {
            \Log::error("Error updating cart: " . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    // public function placeOrder(Request $request)
    // {
    //     try {
    //         $cart = $this->getCartData();
    //         $userId = $this->getUserId();
    //         $ordersFilePath = storage_path('app/orders.json');

    //         // ✅ Fetch only the logged-in user's cart
    //         $userCart = collect($cart)->firstWhere('user_id', $userId);

    //         if (!$userCart || empty($userCart['products'])) {
    //             return response()->json(['message' => 'Cart is empty!'], 400);
    //         }

    //         // ✅ Ensure required fields are present
    //         if (!$request->has(['address', 'shipping_type', 'payment_method'])) {
    //             return response()->json(['message' => 'Missing required fields!'], 400);
    //         }

    //         // ✅ Fetch existing orders or create empty array
    //         $existingOrders = file_exists($ordersFilePath) ? json_decode(file_get_contents($ordersFilePath), true) : [];

    //         // ✅ Generate new order ID
    //         $orderId = count($existingOrders) + 1;

    //         // ✅ Prepare order details
    //         $orderDetails = [
    //             'order_id' => $orderId,
    //             'user_id' => $userId,
    //             'cart' => array_values($userCart['products']), // Convert object to array
    //             'total' => $userCart['grand_total'] ?? 0,
    //             'address' => $request->address,
    //             'shipping_type' => $request->shipping_type,
    //             'payment_method' => $request->payment_method,
    //             'order_date' => now()->format('Y-m-d H:i:s'),
    //             'status' => 'pending'
    //         ];

    //         // ✅ Save order in `orders.json`
    //         $existingOrders[] = $orderDetails;
    //         file_put_contents($ordersFilePath, json_encode($existingOrders, JSON_PRETTY_PRINT));

    //         return response()->json(['message' => 'Order placed successfully!', 'order_id' => $orderId]);

    //     } catch (\Exception $e) {
    //         \Log::error("Error placing order: " . $e->getMessage());
    //         return response()->json(['message' => 'Internal Server Error'], 500);
    //     }
    // }



}



