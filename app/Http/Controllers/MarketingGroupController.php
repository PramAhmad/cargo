<?php

namespace App\Http\Controllers;

use App\Models\MarketingGroup;
use Illuminate\Http\Request;

class MarketingGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query
        $search = $request->input('search');
        
        // Build the query
        $marketingGroupsQuery = MarketingGroup::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $marketingGroupsQuery->where('name', 'like', "%{$search}%");
        }
        
        // Order and paginate results
        $marketingGroups = $marketingGroupsQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
            
        return view('backend.marketing-groups.index', compact('marketingGroups', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.marketing-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MarketingGroup::create($validated);

        return redirect()->route('marketing-groups.index')
            ->with('success', 'Marketing Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingGroup $marketingGroup)
    {
        return view('backend.marketing-groups.show', compact('marketingGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingGroup $marketingGroup)
    {
        return view('backend.marketing-groups.edit', compact('marketingGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketingGroup $marketingGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $marketingGroup->update($validated);

        return redirect()->route('marketing-groups.index')
            ->with('success', 'Marketing Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingGroup $marketingGroup)
    {
        $marketingGroup->delete();

        return redirect()->route('marketing-groups.index')
            ->with('success', 'Marketing Group deleted successfully.');
    }
}