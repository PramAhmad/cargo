<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query
        $search = $request->input('search');
        
        // Build the query
        $categoryProductsQuery = CategoryProduct::query();
        
        // Apply search filter if search term is provided
        if ($search) {
            $categoryProductsQuery->where('name', 'like', "%{$search}%");
        }
        
        // Order and paginate results
        $categoryProducts = $categoryProductsQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
            
        return view('backend.category-products.index', compact('categoryProducts', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.category-products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CategoryProduct::create($validated);

        return redirect()->route('category-products.index')
            ->with('success', 'Category Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryProduct $categoryProduct)
    {
        return view('backend.category-products.show', compact('categoryProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryProduct $categoryProduct)
    {
        return view('backend.category-products.edit', compact('categoryProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryProduct $categoryProduct)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $categoryProduct->update($validated);

        return redirect()->route('category-products.index')
            ->with('success', 'Category Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryProduct $categoryProduct)
    {
        $categoryProduct->delete();

        return redirect()->route('category-products.index')
            ->with('success', 'Category Product deleted successfully.');
    }
}
