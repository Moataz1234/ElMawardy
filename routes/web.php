<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewItemController;
use App\Http\Controllers\Gold\GoldItemController;
use App\Http\Controllers\Gold\GoldItemSoldController;
use App\Http\Controllers\Gold\GoldPoundController;
use App\Http\Controllers\ShopsController;

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [ShopsController::class, 'showShopItems'])
    ->middleware('auth')  // Ensure only authenticated users can access
    ->name('dashboard');
Route::middleware('auth','check.shop')->group(function () {
Route::get('/gold-items/shop/{shop}', [ShopsController::class, 'showShopItems'])
    ->name('gold-items.shop');
Route::post('/gold-items/{id}/transfer', [ShopsController::class, 'transferToBranch'])
    ->name('gold-items.transfer');
Route::get('/gold-items/{id}/transfer', [ShopsController::class, 'showTransferForm'])
->name('gold-items.transferForm');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


route::get('/admin/dashboard',[HomeController::class,'index'])->middleware(['auth','admin'])->name('admin-dashboard');
//   for new item form
Route::middleware('admin')->group(function (){
Route::get('/new-item', [NewItemController::class, 'create'])->name('new-item.create');
Route::post('/new-item', [NewItemController::class, 'store'])->name('new-item.store');
Route::get('/search-model', [HomeController::class, 'searchModel'])->name('search.model');

// Route::resource('/gold-items', GoldItemController::class);
Route::get('/gold-items/create', [GoldItemController::class, 'create'])->name('gold-items.create');
Route::post('/gold-items', [GoldItemController::class, 'store'])->name('gold-items.store');
Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');
Route::get('/gold-items/{id}/edit', [GoldItemController::class, 'edit'])->name('gold-items.edit');
Route::put('/gold-items/{id}', [GoldItemController::class, 'update'])->name('gold-items.update');

//                  Sold
Route::get('/gold-items-sold/{id}/edit', [GoldItemSoldController::class, 'edit'])->name('gold-items-sold.edit');
Route::put('/gold-items-sold/{id}', [GoldItemSoldController::class, 'update'])->name('gold-items-sold.update');
Route::post('/gold-items/{id}/mark-as-sold', [GoldItemSoldController::class, 'markAsSold'])->name('gold-items.markAsSold');

Route::post('/gold-items/add-from-factory', [GoldItemController::class, 'addFromFactory'])->name('gold-items.addFromFactory');

Route::post('/gold-items-sold/{id}/mark-as-rest', [GoldItemSoldController::class, 'markAsRest'])->name('gold-items-sold.markAsRest');

   
});
// Route::get('/gold-catalog', [GoldItemController::class,'ThreeView'])->name('gold_catalog.3');
Route::get('/gold-items-sold', [GoldItemSoldController::class, 'index'])->name('gold-items.sold');

Route::post('/gold-items', [GoldItemController::class, 'store'])->name('gold-items.store');
Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');

Route::get('/gold-pounds', [GoldPoundController::class, 'index'])->name('gold-pounds.index');

// when user logged out
// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [HomeController::class, 'checked']);
//     // Other protected routes
// });
Route::post('/gold-items/transfer/{id}', [ShopsController::class, 'transferRequest'])->name('gold-items.transfer');
Route::get('/transfer-request/{id}/{status}', [ShopsController::class, 'handleTransferRequest'])->name('transfer.handle');
// Route to view pending transfer requests
Route::get('/transfer-requests/{id}', [ShopsController::class, 'viewTransferRequests'])->name('transfer.requests');

// Route to handle accepting/rejecting the transfer request
Route::get('/transfer-request/{id}/{status}', [ShopsController::class, 'handleTransferRequest'])->name('transfer.handle');
