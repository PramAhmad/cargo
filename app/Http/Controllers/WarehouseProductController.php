<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    /**
     * Display a listing of the products for a specific warehouse.
     */
    public function index(Request $request, Mitra $mitra, Warehouse $warehouse)
    {
        // Pastikan warehouse milik mitra ini
        if ($warehouse->mitra_id != $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        $query = Product::where('warehouse_id', $warehouse->id)
                        ->whereNull('parent_id');
        
        // Apply category filter if provided
        if ($request->filled('category')) {
            $query->where('category_product_id', $request->category);
        }
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Get products with their children
        $products = $query->with(['children', 'category'])->get();
        
        // Get all categories for filter dropdown
        $categories = CategoryProduct::all();

        return view('backend.mitras.warehouses.products.index', compact('mitra', 'warehouse', 'products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Mitra $mitra, Warehouse $warehouse)
    {
        // Pastikan warehouse milik mitra ini
        if ($warehouse->mitra_id != $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil semua produk untuk dropdown parent
        $products = Product::where('warehouse_id', $warehouse->id)->get();
        
        // Ambil semua kategori produk untuk dropdown
        $categories = CategoryProduct::all();

        return view('backend.mitras.warehouses.products.create', compact('mitra', 'warehouse', 'products', 'categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request, Mitra $mitra, Warehouse $warehouse)
    {
        // Pastikan warehouse milik mitra ini
        if ($warehouse->mitra_id != $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'mit_price_cbm' => 'nullable|numeric|min:0',
            'mit_price_kg' => 'nullable|numeric|min:0',
            'cust_price_cbm' => 'nullable|numeric|min:0',
            'cust_price_kg' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:products,id',
            'category_product_id' => 'nullable|exists:category_products,id',
        ]);

        // Validate that if parent_id is provided, it belongs to this warehouse
        if ($request->parent_id) {
            $parent = Product::findOrFail($request->parent_id);
            if ($parent->warehouse_id != $warehouse->id) {
                return redirect()->back()->with('error', 'Selected parent product does not belong to this warehouse.');
            }
        }

        // Pastikan nilai harga adalah numerik dan valid
        $mitPriceCbm = $this->parseFormattedNumber($request->mit_price_cbm);
        $mitPriceKg = $this->parseFormattedNumber($request->mit_price_kg);
        $custPriceCbm = $this->parseFormattedNumber($request->cust_price_cbm);
        $custPriceKg = $this->parseFormattedNumber($request->cust_price_kg);

        // Create product
        Product::create([
            'name' => $request->name,
            'mit_price_cbm' => $mitPriceCbm,
            'mit_price_kg' => $mitPriceKg,
            'cust_price_cbm' => $custPriceCbm,
            'cust_price_kg' => $custPriceKg,
            'parent_id' => $request->parent_id,
            'warehouse_id' => $warehouse->id,
            'category_product_id' => $request->category_product_id,
        ]);

        return redirect()
            ->route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id])
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Mitra $mitra, Warehouse $warehouse, Product $product)
    {
        // Pastikan warehouse milik mitra ini dan product milik warehouse ini
        if ($warehouse->mitra_id != $mitra->id || $product->warehouse_id != $warehouse->id) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil semua produk untuk dropdown parent (kecuali dirinya sendiri dan anak-anaknya)
        $potentialParents = Product::where('warehouse_id', $warehouse->id)
                                  ->where('id', '!=', $product->id)
                                  ->whereNotIn('parent_id', [$product->id])
                                  ->get();
                                  
        // Ambil semua kategori produk untuk dropdown
        $categories = CategoryProduct::all();

        return view('backend.mitras.warehouses.products.edit', compact('mitra', 'warehouse', 'product', 'potentialParents', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Mitra $mitra, Warehouse $warehouse, Product $product)
    {
        // Pastikan warehouse milik mitra ini dan product milik warehouse ini
        if ($warehouse->mitra_id != $mitra->id || $product->warehouse_id != $warehouse->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'mit_price_cbm' => 'nullable|numeric|min:0',
            'mit_price_kg' => 'nullable|numeric|min:0',
            'cust_price_cbm' => 'nullable|numeric|min:0',
            'cust_price_kg' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:products,id',
            'category_product_id' => 'nullable|exists:category_products,id',
        ]);

        // Validate that parent_id is not the product itself or its children
        if ($request->parent_id) {
            if ($request->parent_id == $product->id) {
                return redirect()->back()->with('error', 'Product cannot be its own parent.');
            }

            // Cek apakah parent_id merupakan salah satu child dari product
            $childIds = $product->children()->pluck('id')->toArray();
            if (in_array($request->parent_id, $childIds)) {
                return redirect()->back()->with('error', 'Cannot set a child product as parent.');
            }

            // Pastikan parent ada di warehouse yang sama
            $parent = Product::findOrFail($request->parent_id);
            if ($parent->warehouse_id != $warehouse->id) {
                return redirect()->back()->with('error', 'Selected parent product does not belong to this warehouse.');
            }
        }

        // Pastikan nilai harga adalah numerik dan valid
        $mitPriceCbm = $this->parseFormattedNumber($request->mit_price_cbm);
        $mitPriceKg = $this->parseFormattedNumber($request->mit_price_kg);
        $custPriceCbm = $this->parseFormattedNumber($request->cust_price_cbm);
        $custPriceKg = $this->parseFormattedNumber($request->cust_price_kg);

        // Update product
        $product->update([
            'name' => $request->name,
            'mit_price_cbm' => $mitPriceCbm,
            'mit_price_kg' => $mitPriceKg,
            'cust_price_cbm' => $custPriceCbm,
            'cust_price_kg' => $custPriceKg,
            'parent_id' => $request->parent_id,
            'category_product_id' => $request->category_product_id,
        ]);

        return redirect()
            ->route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id])
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Mitra $mitra, Warehouse $warehouse, Product $product)
    {
        // Pastikan warehouse milik mitra ini dan product milik warehouse ini
        if ($warehouse->mitra_id != $mitra->id || $product->warehouse_id != $warehouse->id) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah product memiliki children
        $hasChildren = $product->children()->exists();
        if ($hasChildren) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with sub-products. Please delete all sub-products first.'
            ], 422);
        }

        // Delete product
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    /**
     * Parse formatted number to float
     * Handles both client-side formatted numbers and potentially direct inputs
     * 
     * @param string|null $number
     * @return float
     */
    private function parseFormattedNumber($number)
    {
        // Default to 0 if null or empty
        if ($number === null || $number === '') {
            return 0;
        }
        
        // Check if this is already a numeric value
        if (is_numeric($number)) {
            return (float) $number;
        }
        
        // Try to handle various formats (1.000,50 or 1,000.50 or 1 000,50)
        // First, remove any non-numeric characters except decimal separator and thousands separator
        $number = preg_replace('/[^0-9,.]/s', '', $number);
        
        // Handle European format (1.000,50)
        if (substr_count($number, ',') === 1 && substr_count($number, '.') > 0) {
            // Replace dots (thousand separators) with nothing, then replace comma with dot
            $number = str_replace('.', '', $number);
            $number = str_replace(',', '.', $number);
        } 
        // Handle US/UK format with commas as thousand separators (1,000.50)
        else if (substr_count($number, '.') === 1 && substr_count($number, ',') > 0) {
            // Replace commas (thousand separators) with nothing
            $number = str_replace(',', '', $number);
        }
        // Handle format with comma as decimal separator without thousand separator (1000,50)
        else if (substr_count($number, ',') === 1 && substr_count($number, '.') === 0) {
            // Replace comma with dot
            $number = str_replace(',', '.', $number);
        }
        
        // Convert to float, defaulting to 0 if not a valid number
        $result = floatval($number);
        return is_nan($result) ? 0 : $result;
    }
}