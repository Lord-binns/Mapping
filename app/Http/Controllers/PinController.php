<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:incident,dumping,flood,water,hotspot',
            'image' => 'nullable|image|max:5120', // Up to 5MB image
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('pins', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        } else {
            $validated['user_id'] = null;
        }
        $validated['status'] = 'pending';
        $validated['is_public'] = false;

        Pin::create($validated);

        return response()->json(['success' => true, 'message' => 'Report sent to admin for approval!']);
    }
}

