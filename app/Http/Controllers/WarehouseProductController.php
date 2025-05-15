<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    public function index(Request $request, Mitra $mitra, Warehouse $warehouse)
    {
        if ($warehouse->mitra_id != $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        $query = Product::where('warehouse_id', $warehouse->id)
                        ->whereNull('parent_id');
        
        if ($request->filled('category')) {
            $query->where('category_product_id', $request->category);
        }
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->with(['children', 'category.mitra'])->get();
        
        $categories = CategoryProduct::where('mitra_id', $mitra->id)->get();

        return view('backend.mitras.warehouses.products.index', compact('mitra', 'warehouse', 'products', 'categories'));
    }

    public function create(Mitra $mitra, Warehouse $warehouse)
    {
        if ($warehouse->mitra_id != $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        $products = Product::where('warehouse_id', $warehouse->id)->get();
        
        $categories = CategoryProduct::where('mitra_id', $mitra->id)->orderBy('name')->get();

        return view('backend.mitras.warehouses.products.create', compact('mitra', 'warehouse', 'products', 'categories'));
    }

    public function store(Request $request, Mitra $mitra, Warehouse $warehouse)
    {
        if ($warehouse->mitra_id != $mitra->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:products,id',
            'category_product_id' => 'required|exists:category_products,id'
        ]);

        if ($request->parent_id) {
            $parent = Product::findOrFail($request->parent_id);
            if ($parent->warehouse_id != $warehouse->id) {
                return redirect()->back()->with('error', 'Selected parent product does not belong to this warehouse.');
            }
        }

        $category = CategoryProduct::findOrFail($request->category_product_id);
        if ($category->mitra_id != $mitra->id) {
            return redirect()->back()->with('error', 'Selected category does not belong to this mitra.');
        }

        Product::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'warehouse_id' => $warehouse->id,
            'category_product_id' => $request->category_product_id,
        ]);

        return redirect()
            ->route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id])
            ->with('success', 'Product created successfully.');
    }

    public function edit(Mitra $mitra, Warehouse $warehouse, Product $product)
    {
        if ($warehouse->mitra_id != $mitra->id || $product->warehouse_id != $warehouse->id) {
            abort(403, 'Unauthorized action.');
        }

        $potentialParents = Product::where('warehouse_id', $warehouse->id)
                                  ->where('id', '!=', $product->id)
                                  ->whereNotIn('parent_id', [$product->id])
                                  ->get();
                                  
        $categories = CategoryProduct::where('mitra_id', $mitra->id)->orderBy('name')->get();

        return view('backend.mitras.warehouses.products.edit', compact('mitra', 'warehouse', 'product', 'potentialParents', 'categories'));
    }

    public function update(Request $request, Mitra $mitra, Warehouse $warehouse, Product $product)
    {
        if ($warehouse->mitra_id != $mitra->id || $product->warehouse_id != $warehouse->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:products,id',
            'category_product_id' => 'required|exists:category_products,id',
        ]);

        if ($request->parent_id) {
            if ($request->parent_id == $product->id) {
                return redirect()->back()->with('error', 'Product cannot be its own parent.');
            }

            $childIds = $product->children()->pluck('id')->toArray();
            if (in_array($request->parent_id, $childIds)) {
                return redirect()->back()->with('error', 'Cannot set a child product as parent.');
            }

            $parent = Product::findOrFail($request->parent_id);
            if ($parent->warehouse_id != $warehouse->id) {
                return redirect()->back()->with('error', 'Selected parent product does not belong to this warehouse.');
            }
        }

        $category = CategoryProduct::findOrFail($request->category_product_id);
        if ($category->mitra_id != $mitra->id) {
            return redirect()->back()->with('error', 'Selected category does not belong to this mitra.');
        }

        $product->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'category_product_id' => $request->category_product_id,
        ]);

        return redirect()
            ->route('mitra.warehouses.products.index', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id])
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Mitra $mitra, Warehouse $warehouse, Product $product)
    {
        if ($warehouse->mitra_id != $mitra->id || $product->warehouse_id != $warehouse->id) {
            abort(403, 'Unauthorized action.');
        }

        $hasChildren = $product->children()->exists();
        if ($hasChildren) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with sub-products. Please delete all sub-products first.'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}