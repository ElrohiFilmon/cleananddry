<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FAQ;

class FAQController extends Controller
{
    // List all FAQs
    public function index()
    {
        $faqs = FAQ::orderBy('order', 'asc')->get();
        return response()->json($faqs);
    }

    // Show a single FAQ
    public function show($id)
    {
        $faq = FAQ::find($id);
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }
        return response()->json($faq);
    }

    // Admin: create FAQ
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer'
        ]);

        $faq = FAQ::create($validated);
        return response()->json(['message' => 'FAQ created', 'faq' => $faq], 201);
    }

    // Admin: update FAQ
    public function update(Request $request, $id)
    {
        $faq = FAQ::find($id);
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        $validated = $request->validate([
            'question' => 'sometimes|string|max:255',
            'answer' => 'sometimes|string',
            'order' => 'nullable|integer'
        ]);

        $faq->update($validated);
        return response()->json(['message' => 'FAQ updated', 'faq' => $faq]);
    }

    // Admin: delete FAQ
    public function destroy($id)
    {
        $faq = FAQ::find($id);
        if (!$faq) {
            return response()->json(['message' => 'FAQ not found'], 404);
        }

        $faq->delete();
        return response()->json(['message' => 'FAQ deleted']);
    }
}