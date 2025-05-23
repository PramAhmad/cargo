<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    /**
     * Get warehouse details including associated categories from its mitra
     *
     * @param int $warehouseId
     * @param Request $request
     * @return JsonResponse
     */
    public function getWarehouseDetails(int $warehouseId, Request $request): JsonResponse
    {
        $warehouse = Warehouse::with('mitra')->findOrFail($warehouseId);
        
        // Get service type from request (default to sea if not provided)
        $serviceType = strtolower($request->input('service', 'sea'));
        
        // Get categories belonging to the warehouse's mitra
        $categories = CategoryProduct::where('mitra_id', $warehouse->mitra_id)
            ->orderBy('name')
            ->get()
            ->map(function ($category) use ($serviceType) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    // Sea or Air pricing based on the selected service
                    'mit_price_cbm_' . $serviceType => $category->getPriceForMode($serviceType, 'mit', 'cbm'),
                    'mit_price_kg_' . $serviceType => $category->getPriceForMode($serviceType, 'mit', 'kg'),
                    'cust_price_cbm_' . $serviceType => $category->getPriceForMode($serviceType, 'cust', 'cbm'),
                    'cust_price_kg_' . $serviceType => $category->getPriceForMode($serviceType, 'cust', 'kg'),
                ];
            });
        
        // Prepare response data
        $response = [
            'warehouse' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'type' => $warehouse->type,
                'address' => $warehouse->address,
                'status' => $warehouse->status,
                'created_at' => $warehouse->created_at,
                'updated_at' => $warehouse->updated_at,
            ],
            'mitra' => [
                'id' => $warehouse->mitra->id,
                'name' => $warehouse->mitra->name,
                'code' => $warehouse->mitra->code,
            ],
            'categories' => $categories
        ];
        
        return response()->json($response);
    }

    /**
     * Get categories and products for a specific warehouse
     *
     * @param int $warehouseId
     * @param Request $request
     * @return JsonResponse
     */
    public function getWarehouseProducts(int $warehouseId, Request $request): JsonResponse
    {
        // Get the warehouse with its mitra
        $warehouse = Warehouse::with('mitra')->findOrFail($warehouseId);
        
        // Get service type from request (default to sea if not provided)
        $serviceType = strtolower($request->input('service', 'sea'));
        
        // Get categories used by products in this warehouse
        $categoryIds = Product::where('warehouse_id', $warehouseId)
            ->whereNotNull('category_product_id')
            ->distinct()
            ->pluck('category_product_id');
            
        // Get the full category information
        $categories = CategoryProduct::whereIn('id', $categoryIds)
            ->where('mitra_id', $warehouse->mitra_id)
            ->orderBy('name')
            ->get()
            ->map(function ($category) use ($warehouseId, $serviceType) {
                // Get products for this category in this warehouse
                $products = Product::with('category')
                    ->where('warehouse_id', $warehouseId)
                    ->where('category_product_id', $category->id)
                    ->orderBy('name')
                    ->get()
                    ->map(function ($product) use ($category, $serviceType) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name ?? 'kosong',
                            'category_id' => $product->category_product_id,
                            // Use the correct fields based on service type
                            'price_cbm' => $product->category ? $product->category->getPriceForMode($serviceType, 'mit', 'cbm') : 0,
                            'price_kg' => $product->category ? $product->category->getPriceForMode($serviceType, 'mit', 'kg') : 0,
                            'cust_price_cbm' => $product->category ? $product->category->getPriceForMode($serviceType, 'cust', 'cbm') : 0,
                            'cust_price_kg' => $product->category ? $product->category->getPriceForMode($serviceType, 'cust', 'kg') : 0,
                            'parent_id' => $product->parent_id,
                            'label' => $product->label
                        ];
                    });
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    // Use the correct pricing fields based on service type
                    'mit_price_cbm_' . $serviceType => $category->getPriceForMode($serviceType, 'mit', 'cbm'),
                    'mit_price_kg_' . $serviceType => $category->getPriceForMode($serviceType, 'mit', 'kg'),
                    'cust_price_cbm_' . $serviceType => $category->getPriceForMode($serviceType, 'cust', 'cbm'),
                    'cust_price_kg_' . $serviceType => $category->getPriceForMode($serviceType, 'cust', 'kg'),
                    'products' => $products
                ];
            });
        
        // Also get products without a category
        $uncategorizedProducts = Product::with('category')
            ->where('warehouse_id', $warehouseId)
            ->whereNull('category_product_id')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? 'kosong',
                    'category_id' => null,
                    'price_cbm' => 0,
                    'price_kg' => 0,
                    'cust_price_cbm' => 0,
                    'cust_price_kg' => 0,
                    'parent_id' => $product->parent_id,
                    'label' => $product->label
                ];
            });
        
        // If there are uncategorized products, add them to a special category
        if ($uncategorizedProducts->count() > 0) {
            $categories->push([
                'id' => 0,
                'name' => 'Tanpa Kategori',
                'mit_price_cbm_' . $serviceType => 0,
                'mit_price_kg_' . $serviceType => 0,
                'cust_price_cbm_' . $serviceType => 0,
                'cust_price_kg_' . $serviceType => 0,
                'products' => $uncategorizedProducts
            ]);
        }
        
        return response()->json([
            'warehouse' => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'type' => $warehouse->type
            ],
            'categories' => $categories
        ]);
    }

    /**
     * Get products for a specific warehouse and category
     *
     * @param int $warehouseId
     * @param int $categoryId
     * @param Request $request
     * @return JsonResponse
     */
    public function getProductsByCategory(int $warehouseId, int $categoryId, Request $request): JsonResponse
    {
        // Get the warehouse
        $warehouse = Warehouse::findOrFail($warehouseId);
        
        // Get service type from request (default to sea if not provided)
        $serviceType = strtolower($request->input('service', 'sea'));
        
        // Get category
        $category = CategoryProduct::findOrFail($categoryId);
        
        // Check if category belongs to warehouse's mitra
        if ($category->mitra_id !== $warehouse->mitra_id) {
            return response()->json([
                'error' => 'The requested category does not belong to this warehouse\'s mitra.'
            ], 403);
        }
        
        // Get products for this warehouse and category
        $products = Product::where('warehouse_id', $warehouseId)
            ->where('category_product_id', $categoryId)
            ->orderBy('name')
            ->get()
            ->map(function ($product) use ($category, $serviceType) {
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? 'kosong',
                    'category_id' => $category->id,
                    'parent_id' => $product->parent_id,
                    'label' => "{$product->name} ({$category->name})",
                    // Include pricing information for the request service type
                    'price_cbm' => $category->getPriceForMode($serviceType, 'mit', 'cbm'),
                    'price_kg' => $category->getPriceForMode($serviceType, 'mit', 'kg'),
                    'cust_price_cbm' => $category->getPriceForMode($serviceType, 'cust', 'cbm'),
                    'cust_price_kg' => $category->getPriceForMode($serviceType, 'cust', 'kg')
                ];
            });
        
        return response()->json($products);
    }
}