<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // List all orders for the authenticated user
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    // Show a single order (details)
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    // Create a new order
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'vehicle' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order = new Order();
        $order->user_id = $user->id;
        $order->service_id = $validated['service_id'];
        $order->scheduled_at = $validated['scheduled_at'];
        $order->vehicle = $validated['vehicle'];
        $order->notes = $validated['notes'] ?? null;
        $order->status = 'pending';
        $order->save();

        return response()->json(['message' => 'Order created', 'order' => $order], 201);
    }

    // Update order status (for admin/washer)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Only allow if admin/washer or the owner of the order
        if ($user->role !== 'washer' && $user->id !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json(['message' => 'Order updated', 'order' => $order]);
    }
}