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

    // public function getCart()
    // {
    //     $cart = $this->getCartData(); // ✅ Get all cart data
    //     $products = $this->getProductData(); // ✅ Get product data
    
    //     $userId = request()->header('X-User-ID'); // ✅ Get user ID from headers
    
    //     if (!$userId) {
    //         return response()->json(["error" => "User ID not found!"], 400);
    //     }
    
    //     \Log::info("Fetching cart for User ID: " . $userId);
    
    //     // ✅ Find the correct cart by `user_id`
    //     $userCart = collect($cart)->firstWhere('user_id', $userId);
    
    //     if (!$userCart) {
    //         return response()->json([
    //             [
    //                 "cart_id" => null,
    //                 "user_id" => $userId,
    //                 "products" => [],
    //                 "Total" => 0,
    //                 "date" => date('Y-m-d H:i:s')
    //             ]
    //         ]);
    //     }
    
    //     // ✅ Attach product details for display
    //     foreach ($userCart['products'] as &$product) {
    //         $productDetails = collect($products)->firstWhere('id', $product['product_id']);
    //         if ($productDetails) {
    //             $product['name'] = $productDetails['name'];
    //             $product['image'] = $productDetails['image'];
    //             $product['sku'] = $productDetails['sku'];
    //         }
    //     }
    
    //     return response()->json([$userCart]);
    // }
    public function getCart()
    {
        $cart = $this->getCartData(); // ✅ Get all cart data
        $products = $this->getProductData(); // ✅ Get product data

        $userId = request()->header('X-User-ID'); // ✅ Get user ID from headers

        if (!$userId) {
            return response()->json(["error" => "User ID not found!"], 400);
        }

        \Log::info("Fetching cart for User ID: " . $userId);

        // ✅ Ensure `user_id` in cart.json is always treated as a string
        $userCart = collect($cart)->firstWhere('user_id', (string) $userId);

        if (!$userCart) {
            return response()->json([
                [
                    "cart_id" => null,
                    "user_id" => $userId,
                    "products" => [],
                    "Total" => 0,
                    "date" => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // ✅ Attach product details for display
        foreach ($userCart['products'] as &$product) {
            $productDetails = collect($products)->firstWhere('id', $product['product_id']);
            if ($productDetails) {
                $product['name'] = $productDetails['name'];
                $product['image'] = $productDetails['image'];
                $product['sku'] = $productDetails['sku'];
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
        $userId = request()->header('X-User-ID'); // ✅ Fetch user ID from request headers
    
        if ($userId) {
            \Log::info("User ID from headers: " . $userId);
            return (string) $userId;
        }
    
        // ✅ If no user ID in headers, assign a guest ID
        $guestId = "guest_" . session()->getId();
        \Log::info("Guest User ID: " . $guestId);
        return $guestId;
    }
    
    public function addToCart($id)
    {
        try {
            $products = $this->getProductData();
            $cart = $this->getCartData();
            $product = collect($products)->firstWhere('id', (int) $id);

            if (!$product) {
                return response()->json(['error' => 'Product not found!'], 404);
            }

            $cartId = request()->header('X-Cart-ID'); // Existing cart ID (if any)
            $userId = request()->header('X-User-ID'); // User ID (logged-in or guest)

            // ✅ If no user is logged in, assign a unique guest ID
            if (!$userId) {
                $userId = "guest_" . bin2hex(random_bytes(4));
            }

            // ✅ Find the highest `cart_id` and increment it by 1
            $existingCartIds = array_column($cart, 'cart_id');
            $newCartId = empty($existingCartIds) ? 1 : max($existingCartIds) + 1;

            // ✅ Check if the user already has a cart
            $existingCartIndex = collect($cart)->search(fn($c) => $c['user_id'] == $userId);

            if ($existingCartIndex !== false) {
                // ✅ User already has a cart, update it
                $existingCart = &$cart[$existingCartIndex];
                $productExists = collect($existingCart['products'])->firstWhere('product_id', $id);

                if ($productExists) {
                    foreach ($existingCart['products'] as &$prod) {
                        if ($prod['product_id'] == $id) {
                            $prod['quantity'] += 1;
                        }
                    }
                } else {
                    $existingCart['products'][] = [
                        "product_id" => $product['id'],
                        "price" => $product['price'],
                        "quantity" => 1
                    ];
                }

                $existingCart['Total'] = array_reduce($existingCart['products'], fn($sum, $prod) => $sum + ($prod['price'] * $prod['quantity']), 0);
            } else {
                // ✅ Create a new cart for the user/guest
                $cart[] = [
                    "cart_id" => $newCartId, // ✅ Auto-incrementing cart ID
                    "user_id" => $userId,
                    "products" => [
                        [
                            "product_id" => $product['id'],
                            "price" => $product['price'],
                            "quantity" => 1
                        ]
                    ],
                    "Total" => $product['price'],
                    "date" => date('Y-m-d H:i:s')
                ];
            }

            file_put_contents(storage_path('app/cart.json'), json_encode($cart, JSON_PRETTY_PRINT));

            return response()->json(['message' => 'Product added to cart successfully!', 'cart_id' => $newCartId, 'user_id' => $userId]);
        } catch (\Exception $e) {
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
        $cart = $this->getCartData();
        $userId = request()->header('X-User-ID');
    
        if (!$userId) {
            return response()->json(["error" => "User ID not found!"], 400);
        }
    
        \Log::info("Removing product $productId for User ID: " . $userId);
    
        // ✅ Find user cart (Ensure `user_id` is compared as a string)
        foreach ($cart as &$userCart) {
            if (strval($userCart['user_id']) === strval($userId)) {
                // ✅ Remove product
                $userCart['products'] = array_values(array_filter($userCart['products'], function ($product) use ($productId) {
                    return $product['product_id'] != $productId;
                }));
    
                // ✅ Update total
                $userCart['Total'] = array_reduce($userCart['products'], fn($sum, $prod) => $sum + ($prod['price'] * $prod['quantity']), 0);
    
                // ✅ Save updated cart
                file_put_contents(storage_path('app/cart.json'), json_encode($cart, JSON_PRETTY_PRINT));
    
                return response()->json(["message" => "Product removed successfully!", "cart" => $userCart]);
            }
        }
    
        return response()->json(["error" => "Cart not found!"], 404);
    }
    
    public function updateCart($productId, $action)
    {
        $cart = $this->getCartData();
        $userId = request()->header('X-User-ID');
    
        if (!$userId) {
            return response()->json(["error" => "User ID not found!"], 400);
        }
    
        \Log::info("Updating cart for Product ID: $productId, Action: $action, User ID: " . $userId);
    
        // ✅ Find user cart
        foreach ($cart as &$userCart) {
            if (strval($userCart['user_id']) === strval($userId)) {
                foreach ($userCart['products'] as &$product) {
                    if ($product['product_id'] == $productId) {
                        if ($action === "increase") {
                            $product['quantity'] += 1;
                        } elseif ($action === "decrease" && $product['quantity'] > 1) {
                            $product['quantity'] -= 1;
                        }
                    }
                }
    
                // ✅ Update total
                $userCart['Total'] = array_reduce($userCart['products'], fn($sum, $prod) => $sum + ($prod['price'] * $prod['quantity']), 0);
    
                // ✅ Save updated cart
                file_put_contents(storage_path('app/cart.json'), json_encode($cart, JSON_PRETTY_PRINT));
    
                return response()->json(["message" => "Cart updated successfully!", "cart" => $userCart]);
            }
        }
    
        return response()->json(["error" => "Cart not found!"], 404);
    }
    
    public function clearCart()
    {
        $cart = $this->getCartData();
        $userId = request()->header('X-User-ID');
    
        if (!$userId) {
            return response()->json(["error" => "User ID not found!"], 400);
        }
    
        \Log::info("Clearing cart for User ID: " . $userId);
    
        // ✅ Remove the cart for this user
        $cart = array_values(array_filter($cart, fn($userCart) => strval($userCart['user_id']) !== strval($userId)));
    
        // ✅ Save updated cart
        file_put_contents(storage_path('app/cart.json'), json_encode($cart, JSON_PRETTY_PRINT));
    
        return response()->json(["message" => "Cart cleared successfully!"]);
    }
    
        
}



