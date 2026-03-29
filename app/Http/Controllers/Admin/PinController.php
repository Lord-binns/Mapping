<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function index()
    {
        $pins = Pin::with('user')->orderByRaw("FIELD(status, 'pending', 'verified', 'rejected')")->paginate(15);
        return view('admin.pins', compact('pins'));
    }

    public function reports()
    {
        $pending  = Pin::with('user')->where('status', 'pending')->latest()->get();
        $verified = Pin::with('user')->where('status', 'verified')->latest()->get();
        $rejected = Pin::with('user')->where('status', 'rejected')->latest()->get();
        return view('admin.reports', compact('pending', 'verified', 'rejected'));
    }

    public function review(Pin $pin)
    {
        $pin->load('user');

        return view('admin.review', compact('pin'));
    }

    public function heatmap()
    {
        $pins = Pin::select('id', 'latitude', 'longitude', 'type')
            ->where('status', 'verified')
            ->get();

        return view('admin.hotspots', compact('pins'));
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
            'type' => 'required|in:incident,dumping,flood,water,hotspot,biohazard',
            'status' => 'required|in:pending,verified,rejected',
            'rejection_comment' => 'nullable|string|max:1000',
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
            'type' => 'required|in:incident,dumping,flood,water,hotspot,biohazard',
            'status' => 'required|in:pending,verified,rejected',
            'rejection_comment' => 'nullable|string|max:1000',
            'image' => 'nullable|url',
        ]);

        $pin->update($validated);

        return redirect()->route('admin.pins.index')->with('success', 'Pin updated.');
    }

    public function verify(Pin $pin)
    {
        $pin->update([
            'status' => 'verified',
            'rejection_comment' => null,
        ]);
        return redirect()->route('admin.reports')->with('success', 'Pin approved and is now visible on the map.');
    }

    public function reject(Request $request, Pin $pin)
    {
        $validated = $request->validate([
            'rejection_comment' => 'nullable|string|max:1000',
        ]);

        $pin->update([
            'status' => 'rejected',
            'rejection_comment' => $validated['rejection_comment'] ?? null,
        ]);

        return redirect()->route('admin.reports')->with('success', 'Pin has been rejected.');
    }

    public function destroy(Pin $pin)
    {
        $pin->delete();
        return redirect()->route('admin.pins.index')->with('success', 'Pin deleted.');
    }
}

