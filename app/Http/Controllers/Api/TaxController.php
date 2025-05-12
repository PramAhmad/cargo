<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Get all active taxes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $taxes = Tax::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $taxes
        ]);
    }
    
    /**
     * Get a specific tax by name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByName(Request $request)
    {
        $name = $request->input('name');
        
        if (!$name) {
            return response()->json([
                'success' => false,
                'message' => 'Tax name is required'
            ], 400);
        }
        
        $tax = Tax::where('name', $name)
            ->where('is_active', true)
            ->first();
            
        if (!$tax) {
            return response()->json([
                'success' => false,
                'message' => 'Tax not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $tax
        ]);
    }
}
