<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Warehouse;
use App\Models\CountryMitra;
use App\Models\CountryWarehouse;
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
        // Get country names directly from the CountryMitra model
        $countryNames = CountryMitra::where('mitra_id', $mitra->id)
            ->pluck('name')
            ->toArray();
        
        return view('backend.mitras.warehouses.create', compact('mitra', 'countryNames'));
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
            'country_name' => 'required|string',
        ]);

        // Check if this country is associated with the mitra
        $mitraCountry = CountryMitra::where('mitra_id', $mitra->id)
            ->where('name', $request->country_name)
            ->first();
            
        if (!$mitraCountry) {
            return back()->withErrors(['country_name' => 'The selected country is not associated with this mitra.']);
        }

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

        // Create the warehouse
        $warehouse = $mitra->warehouses()->create([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'address_photo' => $validated['address_photo'] ?? null,
            'type' => $validated['type'],
        ]);

        // Associate the country with the warehouse directly with the name
        CountryWarehouse::create([
            'warehouse_id' => $warehouse->id,
            'name' => $request->country_name,
        ]);

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

        // Get country names directly from the CountryMitra model
        $countryNames = CountryMitra::where('mitra_id', $mitra->id)
            ->pluck('name')
            ->toArray();
        
        // Get current warehouse country
        $selectedCountryName = CountryWarehouse::where('warehouse_id', $warehouse->id)
            ->value('name');

        return view('backend.mitras.warehouses.edit', compact('mitra', 'warehouse', 'countryNames', 'selectedCountryName'));
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
            'country_name' => 'required|string',
        ]);

        // Check if this country is associated with the mitra
        $mitraCountry = CountryMitra::where('mitra_id', $mitra->id)
            ->where('name', $request->country_name)
            ->first();
            
        if (!$mitraCountry) {
            return back()->withErrors(['country_name' => 'The selected country is not associated with this mitra.']);
        }

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

        $warehouse->update([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'address_photo' => $validated['address_photo'] ?? $warehouse->address_photo,
            'type' => $validated['type'],
        ]);

        // Update or create the country association with the name directly
        $countryWarehouse = CountryWarehouse::where('warehouse_id', $warehouse->id)->first();
        
        if ($countryWarehouse) {
            $countryWarehouse->update(['name' => $request->country_name]);
        } else {
            CountryWarehouse::create([
                'warehouse_id' => $warehouse->id,
                'name' => $request->country_name,
            ]);
        }

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

        // Delete associated country warehouse record
        CountryWarehouse::where('warehouse_id', $warehouse->id)->delete();

        $warehouse->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Warehouse deleted successfully.',
        ]);
    }
}