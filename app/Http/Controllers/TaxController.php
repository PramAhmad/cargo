<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Enums\TaxType;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query
        $search = $request->input('search');
        
        // Build the query
        $taxesQuery = Tax::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $taxesQuery->where('name', 'like', "%{$search}%");
        }
        
        // Order and paginate results
        $taxes = $taxesQuery->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
            
        return view('backend.taxes.index', compact('taxes', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get tax types for the dropdown
        $taxTypes = [
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount'
        ];
        
        return view('backend.taxes.create', compact('taxTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        // Set default for is_active if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        Tax::create($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        return view('backend.taxes.show', compact('tax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        // Get tax types for the dropdown
        $taxTypes = [
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount'
        ];
        
        return view('backend.taxes.edit', compact('tax', 'taxTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        // Set is_active to false if the checkbox was not checked
        $validated['is_active'] = $request->has('is_active');

        $tax->update($validated);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }

    /**
     * Toggle the active status of a tax.
     */
    public function toggle(Tax $tax)
    {
        $tax->is_active = !$tax->is_active;
        $tax->save();

        $status = $tax->is_active ? true : false;
        return redirect()->route('taxes.index')
            ->with('success', "Tax '{$tax->name}' has been {$status}.");
    }

    /**
     * Update specific fields of a tax record.
     */
    public function updateFields(Request $request, Tax $tax)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|in:percentage,fixed',
                'value' => 'required|numeric|min:0',
                'is_active' => 'required|boolean'
            ]);
            
            $tax->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => "Tax '{$tax->name}' has been updated successfully."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to update tax: " . $e->getMessage()
            ], 422);
        }
    }
}