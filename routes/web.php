<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MitraWarehouseController;
use App\Http\Controllers\ProfileController as UserProfileController;
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

});
Route::middleware(['auth'])->prefix('mitras/{mitra}/warehouses')->name('mitra.warehouses.')->group(function () {
    Route::get('/', [MitraWarehouseController::class, 'index'])->name('index');
    Route::get('/create', [MitraWarehouseController::class, 'create'])->name('create');
    Route::post('/', [MitraWarehouseController::class, 'store'])->name('store');
    Route::get('/{warehouse}/edit', [MitraWarehouseController::class, 'edit'])->name('edit');
    Route::put('/{warehouse}', [MitraWarehouseController::class, 'update'])->name('update');
    Route::delete('/{warehouse}', [MitraWarehouseController::class, 'destroy'])->name('destroy');
});
Route::middleware('auth')->group(function ()
{
});

require __DIR__.'/auth.php';
