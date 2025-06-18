<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopsController;
use App\Http\Controllers\Gold\GoldItemSoldController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Excel\ExcelImportController;
use App\Http\Controllers\DidItemsController;
use App\Http\Controllers\GoldCatalogController;
use App\Http\Controllers\Shop\ShopController;

Route::middleware(['auth', 'user'])->group(function () {
    // Shop Requests from web.php
    Route::get('/shop/requests', [ShopsController::class, 'showAdminRequests'])
        ->name('shop.requests.index');
    Route::patch('/shop/requests/{itemRequest}', [ShopsController::class, 'updateAdminRequests'])
        ->name('shop.requests.update');

    // Shop Dashboard & Items from web.php
    Route::get('/shop/dashboard', [ShopsController::class, 'showShopItems'])->name('shop-dashboard');
    Route::get('/gold-catalog', [GoldCatalogController::class, 'ThreeView'])->name('gold-catalog');
    Route::get('/dashboard/{id}/edit', [ShopsController::class, 'edit'])->name('shop-items.edit');
    Route::get('/gold-items/shop', [ShopsController::class, 'showShopItems'])->name('gold-items.shop');
    Route::post('/gold-items-sold/{id}/mark-as-rest', [GoldItemSoldController::class, 'markAsRest'])->name('gold-items-sold.markAsRest');
    Route::get('/gold-items-sold/{id}/edit', [GoldItemSoldController::class, 'edit'])->name('gold-items-sold.edit');
    Route::put('/gold-items-sold/{id}', [GoldItemSoldController::class, 'update'])->name('gold-items-sold.update');

    Route::post('/gold-items/store-outer', [ShopsController::class, 'storeOuter'])->name('gold-items.storeOuter');
    Route::post('gold-items/returnOuter/{serialNumber}', [ShopsController::class, 'returnOuter'])->name('gold-items.returnOuter');
    Route::post('gold-items/toggleReturn/{serial_number}', [ShopsController::class, 'toggleReturn'])->name('gold-items.toggleReturn');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/orders/history', [OrderController::class, 'showCompletedOrders'])->name('orders.history');
    Route::post('/shop-items/bulk-sell', [ShopsController::class, 'BulkSell'])->name('shop-items.bulkSell');
    Route::get('/shop-items/bulk-sell-form', [ShopsController::class, 'showBulkSellForm'])->name('shop-items.bulkSellForm');

    Route::get('/import', [ExcelImportController::class, 'showForm'])->name('import.form');
    Route::post('/import', [ExcelImportController::class, 'import'])->name('excel.import');

    // Shop Workshop Request routes from web.php
    Route::get('/shop/workshop-requests', [DidItemsController::class, 'shopWorkshopRequests'])->name('shop.workshop.requests');
    Route::post('/shop/workshop-requests/handle', [DidItemsController::class, 'handleShopWorkshopRequests'])->name('shop.workshop.requests.handle');

    // Workshop requests for 'rabea' shop from web.php - This was under user middleware, might need review if it's correct.
    Route::get('/rabea/workshop-requests', [ShopsController::class, 'showWorkshopRequests'])->name('rabea.workshop.requests');
    Route::post('/rabea/workshop-requests/handle', [ShopsController::class, 'handleWorkshopRequests'])->name('rabea.workshop.requests.handle');
    
    // Original shop.php routes (using ShopController)
    // Kept for reference, but prefer routes from web.php using ShopsController if they are the newer ones.
    // I'll use the newer routes from web.php and comment out the old ones to avoid conflicts.
    
    // Shop Dashboard & Items
    // Route::get('/dashboard', [ShopController::class, 'showShopItems'])->name('shop-dashboard'); // Duplicate name
    Route::get('/items', [ShopController::class, 'showShopItems'])->name('shop-items.index');
    // Route::get('/items/{id}/edit', [ShopController::class, 'edit'])->name('shop-items.edit'); // Duplicate name
    
    // Transfer Management
    Route::get('/transfer-requests', [ShopController::class, 'viewTransferRequests'])->name('transfer.requests');
    Route::get('/items/bulk-transfer-form', [ShopController::class, 'showBulkTransferForm'])->name('shop-items.bulkTransferForm');
    Route::post('/bulk-transfer', [ShopController::class, 'bulkTransfer'])->name('shop-items.bulk-transfer');
    
    // Bulk Sell
    // Route::get('/items/bulk-sell-form', [ShopController::class, 'showBulkSellForm'])->name('shop-items.bulkSellForm'); // Duplicate name
    // Route::post('/items/bulk-sell', [ShopController::class, 'bulkSell'])->name('shop-items.bulkSell'); // Duplicate name
    
    // Outer Items Management
    Route::post('/items/store-outer', [ShopController::class, 'storeOuter'])->name('shop-items.storeOuter');
    Route::post('/items/returnOuter/{serialNumber}', [ShopController::class, 'returnOuter'])->name('shop-items.returnOuter');
    Route::post('/items/toggleReturn/{serial_number}', [ShopController::class, 'toggleReturn'])->name('shop-items.toggleReturn');
    
    // Admin Requests
    // Route::get('/admin-requests', [ShopController::class, 'showAdminRequests'])->name('shop.requests.index'); // Duplicate name
    // Route::patch('/admin-requests/{itemRequest}', [ShopController::class, 'updateAdminRequests'])->name('shop.requests.update'); // Duplicate name
    
    // Customer Data
    Route::get('/get-customer-data', [ShopController::class, 'getCustomerData'])->name('customer.data');
}); 