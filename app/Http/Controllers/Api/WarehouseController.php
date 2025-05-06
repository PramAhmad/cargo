<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
   
    public function getWarehouseDetails(int $warehouseId): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($warehouseId);
     
        
        return response()->json($warehouse);
    }
     /**
     * Get all products for a specific warehouse
     *
     * @param int $warehouseId
     * @return JsonResponse
     */

    public function getProducts(int $warehouseId): JsonResponse
    {
        // Get the warehouse
        $warehouse = Warehouse::findOrFail($warehouseId);
        
        // Get products belonging to this warehouse
        $products = Product::where('warehouse_id', $warehouseId)
            ->orderBy('id','desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? 'kosong',
                    'price_cbm' => $product->mit_price_cbm,
                    'price_kg' => $product->mit_price_kg,
                    'label' => $product->label 
                ];
            });
        
        return response()->json($products);
    }
}