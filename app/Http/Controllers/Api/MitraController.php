<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\Mitra;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MitraController extends Controller
{
    /**
     * Get all products for a specific mitra
     *
     * @param int $mitraId
     * @return JsonResponse
     */

    public function index(int $mitraId): JsonResponse
    {
        // Get the mitra
        $mitra = Mitra::findOrFail($mitraId);
        
        // Get all products for this mitra

        
        return response()->json($mitra);
    }

    public function getProducts(int $mitraId): JsonResponse
    {
        // Get the mitra
        $mitra = Mitra::findOrFail($mitraId);
        
        // Get warehouse IDs belonging to this mitra
        $warehouseIds = $mitra->warehouses()->pluck('id')->toArray();
        
        // Get products that belong to these warehouses
        $products = Product::whereIn('warehouse_id', $warehouseIds)
            ->select('id', 'name', 'warehouse_id', 'mit_price_cbm', 'mit_price_kg')
            ->with(['warehouse:id,name,type'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'warehouse' => $product->warehouse ? $product->warehouse->name : '',
                    'warehouse_type' => $product->warehouse ? $product->warehouse->type : '',
                    'price_cbm' => $product->mit_price_cbm,
                    'price_kg' => $product->mit_price_kg,
                    'label' => $product->label // This uses the accessor from your Product model
                ];
            });
        
        return response()->json($products);
    }
    
    /**
     * Get all warehouses for a specific mitra
     *
     * @param int $mitraId
     * @return JsonResponse
     */
    public function getWarehouses(int $mitraId): JsonResponse
    {
        // Get warehouses for the mitra
        $warehouses = Warehouse::where('mitra_id', $mitraId)
            ->select('id', 'name', 'type', 'address')
            ->get()
            ->map(function ($warehouse) {
                return [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                    'type' => $warehouse->type,
                    'address' => $warehouse->address,
                    'products_count' => $warehouse->products_count // Uses the accessor from your Warehouse model
                ];
            });
        
        return response()->json($warehouses);
    }
    
    /**
     * Get a specific product with details
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function getProductDetails(int $productId): JsonResponse
    {
        $product = Product::with(['warehouse:id,name,type'])
            ->findOrFail($productId);
            
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'warehouse' => $product->warehouse ? $product->warehouse->name : null,
            'warehouse_type' => $product->warehouse ? $product->warehouse->type : null,
            'price_cbm' => $product->mit_price_cbm,
            'price_kg' => $product->mit_price_kg,
            'category' => $product->category ? $product->category->name : null,
            'parent' => $product->parent ? $product->parent->name : null
        ]);
    }

    /**
     * Get all categories belonging to a mitra
     * 
     * @param int $mitraId
     * @return JsonResponse
     */
    public function getCategories(int $mitraId): JsonResponse
    {
        $categories = CategoryProduct::where('mitra_id', $mitraId)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'mit_price_cbm' => $category->mit_price_cbm,
                    'mit_price_kg' => $category->mit_price_kg,
                    'cust_price_cbm' => $category->cust_price_cbm,
                    'cust_price_kg' => $category->cust_price_kg,
                ];
            });
        
        return response()->json($categories);
    }
}