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
        // Process the raw price values from hidden fields if available
        $this->processRawPriceValues($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mitra_id' => 'required|exists:mitras,id',
            // SEA pricing
            'mit_price_cbm_sea' => 'required|numeric|min:0',
            'mit_price_kg_sea' => 'required|numeric|min:0',
            'cust_price_cbm_sea' => 'required|numeric|min:0',
            'cust_price_kg_sea' => 'required|numeric|min:0',
            // AIR pricing
            'mit_price_cbm_air' => 'required|numeric|min:0',
            'mit_price_kg_air' => 'required|numeric|min:0',
            'cust_price_cbm_air' => 'required|numeric|min:0',
            'cust_price_kg_air' => 'required|numeric|min:0',
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
            // Process the raw price values from hidden fields if available
            $this->processRawPriceValues($request);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mitra_id' => 'required|exists:mitras,id',
                // SEA pricing
                'mit_price_cbm_sea' => 'required|numeric|min:0',
                'mit_price_kg_sea' => 'required|numeric|min:0',
                'cust_price_cbm_sea' => 'required|numeric|min:0',
                'cust_price_kg_sea' => 'required|numeric|min:0',
                // AIR pricing
                'mit_price_cbm_air' => 'required|numeric|min:0',
                'mit_price_kg_air' => 'required|numeric|min:0',
                'cust_price_cbm_air' => 'required|numeric|min:0',
                'cust_price_kg_air' => 'required|numeric|min:0',
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
                    // SEA pricing
                    'mit_price_cbm_sea' => $category->mit_price_cbm_sea,
                    'mit_price_kg_sea' => $category->mit_price_kg_sea,
                    'cust_price_cbm_sea' => $category->cust_price_cbm_sea,
                    'cust_price_kg_sea' => $category->cust_price_kg_sea,
                    // AIR pricing
                    'mit_price_cbm_air' => $category->mit_price_cbm_air,
                    'mit_price_kg_air' => $category->mit_price_kg_air,
                    'cust_price_cbm_air' => $category->cust_price_cbm_air,
                    'cust_price_kg_air' => $category->cust_price_kg_air,
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
        // Process the raw price values from hidden fields if available
        $this->processRawPriceValues($request);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mitra_id' => 'required|exists:mitras,id',
            // SEA pricing
            'mit_price_cbm_sea' => 'required|numeric|min:0',
            'mit_price_kg_sea' => 'required|numeric|min:0',
            'cust_price_cbm_sea' => 'required|numeric|min:0',
            'cust_price_kg_sea' => 'required|numeric|min:0',
            // AIR pricing
            'mit_price_cbm_air' => 'required|numeric|min:0',
            'mit_price_kg_air' => 'required|numeric|min:0',
            'cust_price_cbm_air' => 'required|numeric|min:0',
            'cust_price_kg_air' => 'required|numeric|min:0',
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
    
    /**
     * Process raw price values from hidden fields.
     * This method handles the formatted input from Cleave.js
     * and ensures the raw numeric values are used for storage.
     */
    private function processRawPriceValues(Request $request)
    {
        // Price fields with their raw value counterparts
        $priceFields = [
            'mit_price_cbm_sea', 'mit_price_kg_sea', 'cust_price_cbm_sea', 'cust_price_kg_sea',
            'mit_price_cbm_air', 'mit_price_kg_air', 'cust_price_cbm_air', 'cust_price_kg_air'
        ];
        
        // Process each price field
        foreach ($priceFields as $field) {
            $rawField = $field . '_raw';
            
            // If raw value is provided, use it
            if ($request->has($rawField) && $request->filled($rawField)) {
                $rawValue = $request->input($rawField);
                
                // If the raw value is a string with a decimal comma, convert it to dot for proper numeric handling
                if (is_string($rawValue) && strpos($rawValue, ',') !== false) {
                    $rawValue = str_replace(',', '.', $rawValue);
                }
                
                // Merge the processed value back into the request
                $request->merge([$field => $rawValue]);
            } 
            // If formatted value has thousand separators, process it
            elseif ($request->has($field) && is_string($request->input($field)) && strpos($request->input($field), '.') !== false) {
                $formattedValue = $request->input($field);
                
                // Remove thousand separators and replace decimal comma with dot
                $rawValue = str_replace('.', '', $formattedValue);
                $rawValue = str_replace(',', '.', $rawValue);
                
                // Merge the processed value back into the request
                $request->merge([$field => $rawValue]);
            }
        }
    }
}