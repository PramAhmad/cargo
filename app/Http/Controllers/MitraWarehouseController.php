<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MitraWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Mitra $mitra)
    {
        $warehouses = $mitra->warehouses;
        return view('backend.mitras.warehouses.index', compact('mitra', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Mitra $mitra)
    {
        return view('backend.mitras.warehouses.create', compact('mitra'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Mitra $mitra)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:55',
            'address' => 'nullable|string',
            'address_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|in:sea,air',
        ]);

        if ($request->hasFile('address_photo')) {
            // Generate a unique filename with original extension
            $file = $request->file('address_photo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '.' . $extension;
            
            // Make sure directory exists
            $directory = public_path('warehouses');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            
            // Move file to public/warehouses directory
            $file->move($directory, $filename);
            
            // Store the relative path in the database
            $validated['address_photo'] = 'warehouses/' . $filename;
        }

        $warehouse = $mitra->warehouses()->create($validated);

        return redirect()->route('mitras.edit', $mitra->id)
            ->with('success', 'Warehouse created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mitra $mitra, Warehouse $warehouse)
    {
        // Ensure the warehouse belongs to the mitra
        if ($warehouse->mitra_id !== $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('backend.mitras.warehouses.edit', compact('mitra', 'warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mitra $mitra, Warehouse $warehouse)
    {
        // Ensure the warehouse belongs to the mitra
        if ($warehouse->mitra_id !== $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:55',
            'address' => 'nullable|string',
            'address_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|in:sea,air',
        ]);

        if ($request->hasFile('address_photo')) {
            // Delete old file if exists
            if ($warehouse->address_photo && File::exists(public_path($warehouse->address_photo))) {
                File::delete(public_path($warehouse->address_photo));
            }
            
            // Generate a unique filename with original extension
            $file = $request->file('address_photo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '.' . $extension;
            
            // Make sure directory exists
            $directory = public_path('warehouses');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
            
            // Move file to public/warehouses directory
            $file->move($directory, $filename);
            
            // Store the relative path in the database
            $validated['address_photo'] = 'warehouses/' . $filename;
        }

        $warehouse->update($validated);

        return redirect()->route('mitras.edit', $mitra->id)
            ->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mitra $mitra, Warehouse $warehouse)
    {
        // Ensure the warehouse belongs to the mitra
        if ($warehouse->mitra_id !== $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete address photo if exists
        if ($warehouse->address_photo && File::exists(public_path($warehouse->address_photo))) {
            File::delete(public_path($warehouse->address_photo));
        }

        $warehouse->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Warehouse deleted successfully.',
        ]);
    }
}