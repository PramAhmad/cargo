<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\ShippingType;

class ShippingController extends Controller
{
    /**
     * Generate a unique invoice number based on shipping type
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateInvoice(Request $request): JsonResponse
    {
        $type = $request->query('type', ShippingType::LCL->value);
        
        $prefix = match($type) {
            ShippingType::LCL->value => 'LCL',
            ShippingType::FCL->value => 'FCL',
            default => 'INV'
        };
        
        $year = date('Y');
        $month = date('m');
        
        $latestInvoice = Shipping::where('invoice', 'like', "{$prefix}/{$year}{$month}/%")
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$latestInvoice) {
            $sequenceNumber = 1;
        } else {
            $parts = explode('/', $latestInvoice->invoice);
            $lastSequence = intval(end($parts));
            $sequenceNumber = $lastSequence + 1;
        }
        
        // Format the invoice number: PREFIX/YYYYMM/SEQUENCE
        $invoice = sprintf('%s/%s%s/%04d', $prefix, $year, $month, $sequenceNumber);
        
        return response()->json(['invoice' => $invoice]);
    }
}