<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Get banks associated with a customer
     *
     * @param Customer $customer
     * @return JsonResponse
     */
    public function getBanks(Customer $customer): JsonResponse
    {
        $banks = $customer->banks()->with('bank')->get();
        
        return response()->json($banks);
    }
}