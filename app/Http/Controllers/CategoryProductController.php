<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use App\Models\Mitra;
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
        $mitraFilter = $request->input('mitra_id');
        
        // Build the query
        $categoryProductsQuery = CategoryProduct::with('mitra');
        
        // Apply filters if provided
        if ($search) {
            $categoryProductsQuery->where('name', 'like', "%{$search}%");
        }
        
        if ($mitraFilter) {
            $categoryProductsQuery->where('mitra_id', $mitraFilter);
        }
        
        // Order and paginate results
        $categoryProducts = $categoryProductsQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links
            
        $mitras = Mitra::orderBy('name')->get();
            
        return view('backend.category-products.index', compact('categoryProducts', 'search', 'mitras', 'mitraFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mitras = Mitra::orderBy('name')->get();
        return view('backend.category-products.create', compact('mitras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'mit_price_cbm' => intval(str_replace('.', '', $request->mit_price_cbm ?? 0)),
            'mit_price_kg' => intval(str_replace('.', '', $request->mit_price_kg ?? 0)),
            'cust_price_cbm' => intval(str_replace('.', '', $request->cust_price_cbm ?? 0)),
            'cust_price_kg' => intval(str_replace('.', '', $request->cust_price_kg ?? 0)),
        ]);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mitra_id' => 'required|exists:mitras,id',
            'mit_price_cbm' => 'nullable|numeric|min:0',
            'mit_price_kg' => 'nullable|numeric|min:0',
            'cust_price_cbm' => 'nullable|numeric|min:0',
            'cust_price_kg' => 'nullable|numeric|min:0',
        ]);

        CategoryProduct::create($validated);

        return redirect()->route('category-products.index')
            ->with('success', 'Category Product created successfully.');
    }

    /**
     * Store a newly created category via AJAX.
     */
    public function storeAjax(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mitra_id' => 'required|exists:mitras,id',
                'mit_price_cbm' => 'nullable|numeric|min:0',
                'mit_price_kg' => 'nullable|numeric|min:0',
                'cust_price_cbm' => 'nullable|numeric|min:0',
                'cust_price_kg' => 'nullable|numeric|min:0',
            ]);

            $category = CategoryProduct::create($validated);
            $category->load('mitra'); // Load the mitra relationship

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'mitra_id' => $category->mitra_id,
                    'mitra_name' => $category->mitra->name,
                    'mit_price_cbm' => $category->mit_price_cbm,
                    'mit_price_kg' => $category->mit_price_kg,
                    'cust_price_cbm' => $category->cust_price_cbm,
                    'cust_price_kg' => $category->cust_price_kg,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryProduct $categoryProduct)
    {
        $categoryProduct->load('mitra');
        return view('backend.category-products.show', compact('categoryProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryProduct $categoryProduct)
    {
        $mitras = Mitra::orderBy('name')->get();
        return view('backend.category-products.edit', compact('categoryProduct', 'mitras'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoryProduct $categoryProduct)
    {
        $request->merge([
            'mit_price_cbm' => intval(str_replace('.', '', $request->mit_price_cbm ?? 0)),
            'mit_price_kg' => intval(str_replace('.', '', $request->mit_price_kg ?? 0)),
            'cust_price_cbm' => intval(str_replace('.', '', $request->cust_price_cbm ?? 0)),
            'cust_price_kg' => intval(str_replace('.', '', $request->cust_price_kg ?? 0)),
        ]);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mitra_id' => 'required|exists:mitras,id',
            'mit_price_cbm' => 'nullable|numeric|min:0',
            'mit_price_kg' => 'nullable|numeric|min:0',
            'cust_price_cbm' => 'nullable|numeric|min:0',
            'cust_price_kg' => 'nullable|numeric|min:0',
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
