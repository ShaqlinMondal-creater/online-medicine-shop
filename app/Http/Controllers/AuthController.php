<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private $filePath = 'users.json';

    // ✅ Load users from JSON file
    private function loadUsers()
    {
        $path = storage_path("app/{$this->filePath}");
        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }

    // ✅ Save users to JSON file
    private function saveUsers($users)
    {
        file_put_contents(storage_path("app/{$this->filePath}"), json_encode($users, JSON_PRETTY_PRINT));
    }

    // ✅ User Registration API
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'image' => 'nullable|string'
        ]);
    
        $users = $this->loadUsers();
    
        // ✅ Fix: Remove the `unique` validation manually
        foreach ($users as $user) {
            if ($user['email'] === $request->email) {
                return response()->json(['message' => 'User already exists'], 400);
            }
        }
    
        // ✅ New User Data
        $newUser = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Keeping plaintext for simplicity
            'role' => 'customer',
            'phone' => $request->phone,
            'image' => $request->image ?? 'users/default.jpg'
        ];
    
        // ✅ Save to JSON File
        $users[] = $newUser;
        file_put_contents(storage_path("app/users.json"), json_encode($users, JSON_PRETTY_PRINT));
    
        return response()->json(['message' => 'User registered successfully', 'user' => $newUser]);
    }
    

    // ✅ User & Admin Login API (Now Stores Session Data)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        $users = $this->loadUsers();
        $authFilePath = storage_path('app/auth.json');
    
        foreach ($users as $user) {
            if ($user['email'] === $request->email && $user['password'] === $request->password) {
                $authToken = base64_encode($user['email'] . '_token'); // ✅ Change token to auth_token
                $startDate = now()->format('Y-m-d H:i:s');
    
                // ✅ Read existing auth data
                $authData = file_exists($authFilePath) ? json_decode(file_get_contents($authFilePath), true) : [];
    
                // ✅ Save authentication data in new format
                $authData[$user['id']] = [
                    "user_id" => $user['id'],
                    "auth_token" => $authToken, // ✅ Renamed from token
                    "user" => $user,
                    "start-date" => $startDate,
                    "end-time" => null,
                    "message" => "Login successful"
                ];
    
                // ✅ Save to auth.json
                file_put_contents($authFilePath, json_encode($authData, JSON_PRETTY_PRINT));
    
                return response()->json([
                    'auth_token' => $authToken, // ✅ Return correct token name
                    'user' => $user,
                    'message' => 'Login successful'
                ]);
            }
        }
    
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    
    
    public function storeAuthLog(Request $request)
    {
        $authFilePath = storage_path('app/auth.json');
        $startDate = now()->format('Y-m-d H:i:s');
    
        // ✅ Read existing auth data
        $authData = file_exists($authFilePath) ? json_decode(file_get_contents($authFilePath), true) : [];
    
        // ✅ Store authentication log with correct format
        $authData[$request->user_id] = [
            "user_id" => $request->user_id,
            "auth_token" => $request->auth_token, // ✅ Changed token to auth_token
            "user" => $request->user,
            "start-date" => $startDate,
            "end-time" => null,
            "message" => "Login successful"
        ];
    
        // ✅ Save to auth.json
        file_put_contents($authFilePath, json_encode($authData, JSON_PRETTY_PRINT));
    
        return response()->json(['message' => 'Auth log stored successfully']);
    }
    

    public function logout(Request $request)
    {
        $authFilePath = storage_path('app/auth.json');
        $endDate = now()->format('Y-m-d H:i:s');
    
        // ✅ Read existing auth data
        if (file_exists($authFilePath)) {
            $authData = json_decode(file_get_contents($authFilePath), true);
    
            if (!is_array($authData)) {
                return response()->json(['message' => 'Invalid auth.json format'], 500);
            }
    
            // ✅ Get user_id from request
            $userId = $request->user_id;
    
            // ✅ Update JSON format correctly
            if (isset($authData[$userId])) {
                $authData[$userId]['auth_token'] = null; // ✅ Nullify auth_token
                $authData[$userId]['end-time'] = $endDate;
                $authData[$userId]['message'] = "Logged Out";
            }
    
            // ✅ Save updated auth.json file
            file_put_contents($authFilePath, json_encode($authData, JSON_PRETTY_PRINT));
        }
    
        return response()->json(['message' => 'Logged out successfully']);
    }
    
    
    
    


}
