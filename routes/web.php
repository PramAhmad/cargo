<?php

use App\Http\Controllers\TaxController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MitraWarehouseController;
use App\Http\Controllers\ProfileController as UserProfileController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\WarehouseProductController;
use App\Models\Shipping;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function ()
{
    return view('welcome');
})->name('home');

Route::middleware('auth')->prefix('dashboard')->group(function ()
{
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('banks', \App\Http\Controllers\BankController::class);
    Route::resource('customer-groups', \App\Http\Controllers\CustomerGroupController::class);
    Route::resource('mitra-groups', \App\Http\Controllers\MitraGroupController::class);
    Route::resource('category-customers', \App\Http\Controllers\CategoryCustomerController::class);
    Route::resource('marketing-groups', \App\Http\Controllers\MarketingGroupController::class);
    Route::resource('marketings',MarketingController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('mitras', \App\Http\Controllers\MitraController::class);
    Route::resource('category-products', \App\Http\Controllers\CategoryProductController::class);
    Route::post('/category-products/store-ajax', [\App\Http\Controllers\CategoryProductController::class, 'storeAjax'])->name('category-products.store-ajax');
    Route::resource('shippings', \App\Http\Controllers\ShippingController::class);
    Route::get('/taxes', [TaxController::class, 'index'])->name('taxes.index');
    Route::post('/taxes/{tax}/toggle', [TaxController::class, 'toggle'])->name('taxes.toggle');
    Route::post('/taxes/{tax}/update-fields', [TaxController::class, 'updateFields'])->name('taxes.update-fields');
});
Route::get('mitras/export', [\App\Http\Controllers\MitraController::class, 'export'])->name('mitras.export');
Route::middleware(['auth'])->prefix('mitras/{mitra}/warehouses')->name('mitra.warehouses.')->group(function () {
    Route::get('/', [MitraWarehouseController::class, 'index'])->name('index');
    Route::get('/create', [MitraWarehouseController::class, 'create'])->name('create');
    Route::post('/', [MitraWarehouseController::class, 'store'])->name('store');
    Route::get('/{warehouse}/edit', [MitraWarehouseController::class, 'edit'])->name('edit');
    Route::put('/{warehouse}', [MitraWarehouseController::class, 'update'])->name('update');
    Route::delete('/{warehouse}', [MitraWarehouseController::class, 'destroy'])->name('destroy');
});
Route::middleware('auth')->group(function () {
    // Warehouse Product Management Routes
    Route::get('/mitras/{mitra}/warehouses/{warehouse}/products', [WarehouseProductController::class, 'index'])
        ->name('mitra.warehouses.products.index');
    
    Route::get('/mitras/{mitra}/warehouses/{warehouse}/products/create', [WarehouseProductController::class, 'create'])
        ->name('mitra.warehouses.products.create');
    Route::post('/mitras/{mitra}/warehouses/{warehouse}/products', [WarehouseProductController::class, 'store'])
        ->name('mitra.warehouses.products.store');
    
    Route::get('/mitras/{mitra}/warehouses/{warehouse}/products/{product}/edit', [WarehouseProductController::class, 'edit'])
        ->name('mitra.warehouses.products.edit');
    Route::put('/mitras/{mitra}/warehouses/{warehouse}/products/{product}', [WarehouseProductController::class, 'update'])
        ->name('mitra.warehouses.products.update');
    
    Route::delete('/mitras/{mitra}/warehouses/{warehouse}/products/{product}', [WarehouseProductController::class, 'destroy'])
        ->name('mitra.warehouses.products.destroy');
    
    // Shipping PDF Routes
    Route::get('/shippings/{shipping}/surat-jalan', [ShippingController::class, 'suratJalan'])->name('shippings.surat-jalan');
    Route::get('/shippings/{shipping}/faktur', [ShippingController::class, 'faktur'])->name('shippings.faktur');
    Route::get('/shippings/{shipping}/invoice', [ShippingController::class, 'invoice'])->name('shippings.invoice');
    // Update shipping status
    Route::post('shippings/{shipping}/update-status', [ShippingController::class, 'updateStatus'])
        ->name('shippings.updateStatus');
});

require __DIR__.'/auth.php';
