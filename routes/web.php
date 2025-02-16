<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProceedCheckoutController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('index'); // Redirecting to the index page for now
})->name('home');

Route::get('/index', function () {
    return view('index');
})->name('index');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/api/store-auth-log', [AuthController::class, 'storeAuthLog']);
Route::post('/api/logout', [AuthController::class, 'logout']);

Route::get('/auth-log', function () {
    $authFilePath = storage_path('app/auth.json');

    if (file_exists($authFilePath)) {
        $data = json_decode(file_get_contents($authFilePath), true);
        return response()->json(array_values($data)); // ✅ Always return an array
    }

    return response()->json([], 404); // ✅ Return an empty array if no auth data
});


Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Dashboard Route
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/all-data', [AdminController::class, 'showAllData'])->name('admin.all-data');
// Admin Dashboard




// Separate APIs for Users, Products, and Orders
// API routes for Products
Route::get('/admin/get-products', [AdminController::class, 'getProducts']);
Route::post('/admin/update-product', [AdminController::class, 'updateProduct']);
Route::delete('/admin/delete-product', [AdminController::class, 'deleteProduct']);

// brand section
Route::get('/api/brands', [BrandController::class, 'getBrands']);

// categories
Route::get('/api/categories', [CategoryController::class, 'getCategories']);

Route::get('/shop', [ShopController::class, 'index'])->name('shop');

Route::get('/api/products', [ShopController::class, 'getProducts']);

Route::get('/api/products', [ShopController::class, 'featuredProducts']);

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.details');

// Static pages
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/about', function () {
    return view('about');
})->name('about');






// Cart Routes

// ✅ Change `add-to-cart/{id}` to accept POST requests
Route::match(['get', 'post'], '/add-to-cart/{id}', [CartController::class, 'addToCart']);
Route::get('/buy-now/{id}', [CartController::class, 'buyNow']);
Route::get('/cart', [CartController::class, 'cart']);
Route::get('/remove-from-cart/{id}', [CartController::class, 'removeFromCart']);
Route::get('/clear-cart', [CartController::class, 'clearCart']);
Route::get('/update-cart/{id}/{action}', [CartController::class, 'updateCart']);
Route::get('/get-cart', [CartController::class, 'getCart']);

Route::get('/proceed-checkout', [ProceedCheckoutController::class, 'proceedCheckout'])->name('proceed.checkout');

// old one
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout');



// Order Routes
Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('place.order');
Route::get('/order-success', function () {
    return view('order-success'); 
})->name('order.success');
Route::get('/get-order', [OrderController::class, 'getOrder']);


// Profile Route
Route::get('/profile/{userId}', [ProfileController::class, 'showProfile'])->name('profile');
Route::get('/orders/{userId}', [ProfileController::class, 'getUserOrders'])->name('orders');
// ✅ Fetch Cart Count API
Route::get('/get-cart-count', [CartController::class, 'getCartCount']);



// buy now

// Route::get('/cart', function () {
//     return view('cart');
// });

