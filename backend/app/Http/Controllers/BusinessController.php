<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    // Update only the business location (lat/lng/address)
    public function updateLocation(Request $request, $id)
    {
        $business = Business::find($id);
        if (!$business) {
            return response()->json(['message' => 'Business not found'], 404);
        }
        if ($business->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validated = $request->validate([
            'location_lat' => 'required|numeric',
            'location_lng' => 'required|numeric',
            'location_address' => 'nullable|string',
        ]);
        $business->update($validated);
        return response()->json(['message' => 'Location updated', 'business' => $business]);
    }
    public function index(Request $request)
    {
        $businesses = Business::with('user')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'businesses' => $businesses
        ]);
    }

    public function show($id)
    {
        $business = Business::with('user', 'services')->find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Business not found'
            ], 404);
        }

        return response()->json([
            'business' => $business
        ]);
    }

    public function update(Request $request, $id)
    {
        $business = Business::find($id);

        if (!$business) {
            return response()->json([
                'message' => 'Business not found'
            ], 404);
        }

        // Check if user owns this business
        if ($business->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location_address' => 'sometimes|string',
            'location_lat' => 'sometimes|numeric',
            'location_lng' => 'sometimes|numeric',
            'phone' => 'sometimes|string',
        ]);

        $business->update($validated);

        return response()->json([
            'message' => 'Business updated successfully',
            'business' => $business
        ]);
    }
}