<?php

namespace App\Http\Controllers;

use App\Models\MitraGroup;
use Illuminate\Http\Request;

class MitraGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mitraGroups = MitraGroup::select('*')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('backend.mitra-groups.index', compact('mitraGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.mitra-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MitraGroup::create($validated);

        return redirect()->route('backend.mitra-groups.index')
            ->with('success', 'Mitra Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MitraGroup $mitraGroup)
    {
        return view('backend.mitra-groups.show', compact('mitraGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MitraGroup $mitraGroup)
    {
        return view('backend.mitra-groups.edit', compact('mitraGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MitraGroup $mitraGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $mitraGroup->update($validated);

        return redirect()->route('backend.mitra-groups.index')
            ->with('success', 'Mitra Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MitraGroup $mitraGroup)
    {
        $mitraGroup->delete();

        return redirect()->route('backend.mitra-groups.index')
            ->with('success', 'Mitra Group deleted successfully.');
    }
}