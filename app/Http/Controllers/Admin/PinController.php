<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function index()
    {
        $pins = Pin::with('user')->orderByRaw("FIELD(status, 'pending', 'verified', 'resolved')")->paginate(15);
        return view('admin.pins', compact('pins'));
    }

    public function reports()
    {
        $pending  = Pin::with('user')->where('status', 'pending')->latest()->get();
        $verified = Pin::with('user')->where('status', 'verified')->latest()->get();
        $resolved = Pin::with('user')->where('status', 'resolved')->latest()->get();
        return view('admin.reports', compact('pending', 'verified', 'resolved'));
    }

    public function create()
    {
        return view('admin.pins-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:incident,dumping,flood,water,hotspot',
            'status' => 'required|in:pending,verified,resolved',
            'image' => 'nullable|url',
        ]);

        $validated['user_id'] = auth()->id();

        Pin::create($validated);

        return redirect()->route('admin.pins.index')->with('success', 'Pin created.');
    }

    public function show(Pin $pin)
    {
        return view('admin.pins-show', compact('pin'));
    }

    public function edit(Pin $pin)
    {
        return view('admin.pins-edit', compact('pin'));
    }

    public function update(Request $request, Pin $pin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:incident,dumping,flood,water,hotspot',
            'status' => 'required|in:pending,verified,resolved',
            'image' => 'nullable|url',
        ]);

        $pin->update($validated);

        return redirect()->route('admin.pins.index')->with('success', 'Pin updated.');
    }

    public function verify(Pin $pin)
    {
        $pin->update(['status' => 'verified']);
        return redirect()->route('admin.pins.index')->with('success', 'Pin approved and is now visible on the map.');
    }

    public function reject(Pin $pin)
    {
        $pin->update(['status' => 'resolved']);
        return redirect()->route('admin.pins.index')->with('success', 'Pin has been rejected.');
    }

    public function destroy(Pin $pin)
    {
        $pin->delete();
        return redirect()->route('admin.pins.index')->with('success', 'Pin deleted.');
    }
}

