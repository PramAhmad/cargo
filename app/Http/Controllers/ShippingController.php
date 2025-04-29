<?php

namespace App\Http\Controllers;

use App\Enums\ShippingType;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Marketing;
use App\Models\Mitra;
use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $marketings = Marketing::orderBy('name')->get();
        $mitras = Mitra::orderBy('name')->get();
        $banks = Bank::orderBy('name')->get();
        
        $ppn =  11;
        
        $dateNow = date('dmy');
        $countOfShipping = Shipping::where('shipping_type', ShippingType::LCL->value)->count() + 1;
        $defaultInvoice = strtoupper(ShippingType::LCL->value) . '-' . $dateNow . str_pad($countOfShipping, 3, '0', STR_PAD_LEFT) . '-1';
        
        return view('backend.shippings.create', compact(
            'customers', 
            'marketings', 
            'mitras', 
            'banks', 
            'ppn',
            'defaultInvoice'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipping $shipping)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipping $shipping)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipping $shipping)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipping $shipping)
    {
        //
    }
}
