<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function showProfile($userId)
    {
        $usersFilePath = storage_path('app/users.json');

        // ✅ Load Users JSON Data
        $usersData = file_exists($usersFilePath) ? json_decode(file_get_contents($usersFilePath), true) : [];

        // ✅ Find the User Data by `userId`
        $user = collect($usersData)->firstWhere('id', (int) $userId);

        if (!$user) {
            return redirect('/login')->with('error', 'User not found! Please login.');
        }

        Log::info("✅ Profile Loaded: User ID - {$userId}");

        return view('profile', compact('user'));
    }

    // ✅ Fetch User Orders
    public function getUserOrders($userId)
    {
        $ordersFilePath = storage_path('app/orders.json');

        // ✅ Load Orders JSON Data
        $ordersData = file_exists($ordersFilePath) ? json_decode(file_get_contents($ordersFilePath), true) : [];

        // ✅ Filter Orders Belonging to this User
        $userOrders = array_values(array_filter($ordersData, fn($order) => $order['user_id'] == $userId));

        return response()->json($userOrders);
    }
}
