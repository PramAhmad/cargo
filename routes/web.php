<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MarketingController;
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
});

Route::middleware('auth')->group(function ()
{
});

require __DIR__.'/auth.php';
