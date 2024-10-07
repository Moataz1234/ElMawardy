<?php

use App\Http\Controllers\{
    HomeController, ProfileController, NewItemController,
    Gold\GoldItemController, Gold\GoldItemSoldController,
    Gold\GoldPoundController, ShopsController, OrderController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Public Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ShopsController::class, 'showShopItems'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('admin-dashboard');
    
    Route::get('/new-item/create', [NewItemController::class, 'create'])->name('new-item.create');
    Route::post('/new-item/store', [NewItemController::class, 'store'])->name('new-item.store');

    // Gold Items
    Route::get('/gold-items/create', [GoldItemController::class, 'create'])->name('gold-items.create');
    Route::post('/gold-items/store', [GoldItemController::class, 'store'])->name('gold-items.store');
    Route::get('/gold-items/{id}/edit', [GoldItemController::class, 'edit'])->name('gold-items.edit');
    Route::put('/gold-items/{id}', [GoldItemController::class, 'update'])->name('gold-items.update');

    // Gold Sold Items
    Route::get('/gold-items-sold/{id}/edit', [GoldItemSoldController::class, 'edit'])->name('gold-items-sold.edit');
    Route::put('/gold-items-sold/{id}', [GoldItemSoldController::class, 'update'])->name('gold-items-sold.update');

    // Transfer Requests
    Route::get('/transfer-requests/history', [ShopsController::class, 'viewTransferRequestHistory'])->name('transfer.requests.history');
    //prices
    Route::get('/update-prices', [GoldItemController::class, 'showUpdateForm'])->name('prices.update.form');
    Route::post('/update-prices', [GoldItemController::class, 'updatePrices'])->name('prices.update');

});

// Shop Routes
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard/{id}/edit', [ShopsController::class, 'edit'])->name('shop-items.edit');
    Route::get('/gold-items/shop', [ShopsController::class, 'showShopItems'])->name('gold-items.shop');
    Route::post('/gold-items/{id}/transfer', [ShopsController::class, 'transferToBranch'])->name('gold-items.transfer');
    Route::post('/gold-items/{id}/mark-as-sold', [ShopsController::class, 'markAsSold'])->name('gold-items.markAsSold');
    Route::post('/gold-items-sold/{id}/mark-as-rest', [GoldItemSoldController::class, 'markAsRest'])->name('gold-items-sold.markAsRest');
    Route::get('/transfer-request/{id}/{status}', [ShopsController::class, 'handleTransferRequest'])->name('transfer.handle');
    // Route to view pending transfer requests
    Route::get('/transfer-requests', [ShopsController::class, 'viewTransferRequests'])->name('transfer.requests');
    Route::post('/gold-items/{id}/transfer', [ShopsController::class, 'transferToBranch'])
    ->name('gold-items.transfer');
    Route::get('/gold-items/{id}/transfer', [ShopsController::class, 'showTransferForm'])
    ->name('gold-items.transferForm');
});

// Rabea Routes
Route::middleware(['auth', 'rabea'])->group(function () {
    Route::get('/orders/rabea', [OrderController::class, 'indexForRabea'])->name('orders.rabea.index');
    Route::get('/orders/rabea/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/rabea/{id}/edit', [OrderController::class, 'edit'])->name('orders.rabea.edit');
    Route::put('/orders/rabea/{id}', [OrderController::class, 'updateOrder'])->name('orders.rabea.update');
    Route::get('/orders/requests', [OrderController::class, 'requests'])->name('orders.requests');
    Route::post('/orders/{order}/accept', [OrderController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{id}/update-status/{status}', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

});

// Common Routes for All Authenticated Users
Route::middleware('auth')->group(function () {
    Route::get('/gold-items-sold', [GoldItemSoldController::class, 'index'])->name('gold-items.sold');
    Route::get('/gold-pounds', [GoldPoundController::class, 'index'])->name('gold-pounds.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
});
Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');

require __DIR__.'/auth.php';
