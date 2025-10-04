<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // List all reviews (optionally filter by service or business)
    public function index(Request $request)
    {
        $query = Review::query();

        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->has('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();
        return response()->json($reviews);
    }

    // Show a single review
    public function show($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        return response()->json($review);
    }

    // Create a new review (user must be logged in, probably for an order they've completed)
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = new Review();
        $review->user_id = $user->id;
        $review->business_id = $validated['business_id'];
        $review->order_id = $validated['order_id'];
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'] ?? null;
        $review->save();

        return response()->json(['message' => 'Review submitted', 'review' => $review], 201);
    }

    // Update review (only by owner)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $review = Review::find($id);

        if (!$review || $review->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return response()->json(['message' => 'Review updated', 'review' => $review]);
    }

    // Delete review (only by owner)
    public function destroy($id)
    {
        $user = Auth::user();
        $review = Review::find($id);

        if (!$review || $review->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized or not found'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }
}