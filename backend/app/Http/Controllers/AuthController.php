<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Update user location (lat/lng)
    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        $user->lat = $validated['lat'];
        $user->lng = $validated['lng'];
        $user->save();
        return response()->json(['message' => 'Location updated', 'user' => $user]);
    }
    // Signup (register)
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:customer,washer',
            // Business fields (optional for customer, required for washer)
            'business_name' => 'required_if:role,washer|string|max:255',
            'business_address' => 'required_if:role,washer|string|max:255',
            'business_lat' => 'required_if:role,washer|numeric',
            'business_lng' => 'required_if:role,washer|numeric',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role']
        ]);

        // If washer, create business record
        if ($validated['role'] === 'washer') {
            \App\Models\Business::create([
                'user_id' => $user->id,
                'name' => $validated['business_name'],
                'location_address' => $validated['business_address'],
                'location_lat' => $request->input('business_lat'),
                'location_lng' => $request->input('business_lng'),
                'is_active' => true,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Signup successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // Auth check
    public function checkAuth(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'authenticated' => !!$user,
            'user' => $user
        ]);
    }
}