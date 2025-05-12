<?php

use App\Http\Controllers\Api\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MitraController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\WarehouseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/customers/{customer}/banks', [CustomerController::class, 'getBanks']);
Route::get('/taxes', [\App\Http\Controllers\Api\TaxController::class, 'index']);
// Mitra API routes
Route::get('/mitras/{mitraId}', [MitraController::class, 'index']);
Route::get('/mitras/{mitraId}/products', [MitraController::class, 'getProducts']);
Route::get('/mitras/{mitraId}/warehouses', [MitraController::class, 'getWarehouses']);
Route::get('/products/{productId}', [MitraController::class, 'getProductDetails']);
Route::get('/shippings/generate-invoice', [ShippingController::class, 'generateInvoice']);
Route::get('/warehouses/{warehouseId}', [WarehouseController::class, 'getWarehouseDetails']);
Route::get('/warehouses/{warehouseId}/products', [WarehouseController::class, 'getProducts']);

