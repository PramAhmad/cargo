<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query
        $search = $request->input('search');
        
        // Build the query
        $customerGroupsQuery = CustomerGroup::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $customerGroupsQuery->where('name', 'like', "%{$search}%");
        }
        
        // Order and paginate results
        $customerGroups = $customerGroupsQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
            
        return view('backend.customer-groups.index', compact('customerGroups', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.customer-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CustomerGroup::create($validated);

        return redirect()->route('customer-groups.index')
            ->with('success', 'Customer Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerGroup $customerGroup)
    {
        return view('backend.customer-groups.show', compact('customerGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerGroup $customerGroup)
    {
        return view('backend.customer-groups.edit', compact('customerGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customerGroup->update($validated);

        return redirect()->route('customer-groups.index')
            ->with('success', 'Customer Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerGroup $customerGroup)
    {
        $customerGroup->delete();

        return redirect()->route('customer-groups.index')
            ->with('success', 'Customer Group deleted successfully.');
    }
}