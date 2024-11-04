<?php

use App\Http\Controllers\{
    HomeController, ProfileController, NewItemController,
    Gold\GoldItemController, Gold\GoldItemSoldController,
    Gold\GoldPoundController, ShopsController, OrderController,ShopifyProductController ,GoldReportController,RabiaController,Auth\AsgardeoAuthController,OuterController,GoldCatalogController,
};
use Illuminate\Support\Facades\Route;


// Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'redirectToProvider'])->name('login');
// Route::get('/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleProviderCallback']);

// web.php
// Route::get('/shopify/product/update-price', [ShopifyProductController::class, 'showUpdatePriceForm'])->name('shopify.product.updatePriceForm');
Route::post('/shopify/product/update-price', [ShopifyProductController::class, 'updatePrices'])->name('shopify.updatePrices');
Route::get('/shopify/product/update-price', function () {
    return view('shopify.update_price'); // Adjust the path if needed
})->name('shopify.product.updatePricesForm');

Route::get('/gold-items/weight-analysis', [GoldItemController::class, 'analyzeWeights'])->name('gold-items.weight-analysis');
Route::get('/gold-items-sold', [GoldItemSoldController::class, 'index'])->name('gold-items-sold.index');

// Public Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [ShopsController::class, 'showShopItems'])->name('dashboard');
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
    Route::put('/gold-items-sold/{id}', [GoldItemSoldController::class, 'update'])->name('gold-items-sold.update');

    // Transfer Requests
    Route::get('/transfer-requests/history', [ShopsController::class, 'viewTransferRequestHistory'])->name('transfer.requests.history');
    //prices
    Route::get('/update-prices', [GoldItemController::class, 'showUpdateForm'])->name('prices.update.form');
    Route::post('/update-prices', [GoldItemController::class, 'updatePrices'])->name('prices.update');

    //Shopify 
    Route::get('/shopify-products', [ShopifyProductController::class, 'index'])->name('shopify.products');
    Route::get('/shopify-products/orders', [ShopifyProductController::class, 'Order_index'])->name('orders_shopify');
    Route::post('/shopify/orders/{id}/fulfill', [ShopifyProductController::class, 'fulfillOrder'])->name('fulfill_order');
    Route::post('/shopify/orders/{id}/paid', [ShopifyProductController::class, 'markAsPaid'])->name('mark_as_paid');
    Route::get('/orders/{orderId}/pdf', [ShopifyProductController::class,'generatePDF'])->name('order.pdf');
    Route::get('/shopify-products/abandoned-checkouts', [ShopifyProductController::class, 'AbandonedCheckouts_index'])->name('abandoned_checkouts_shopify');
    Route::get('/shopify-products/edit/{product_id}', [ShopifyProductController::class, 'showEditImageForm'])->name('shopify.products.showEditImageForm');
    Route::post('/shopify-products/edit/{product_id}', [ShopifyProductController::class, 'editProduct'])->name('shopify.products.editProduct');

    //Gold Report
    Route::get('/gold-report', [GoldReportController::class, 'index'])->name('gold.report');

});
// Shop Routes
Route::middleware(['auth', 'user'])->group(function () {
    
    Route::get('/gold-catalog', [GoldCatalogController::class, 'ThreeView'])->name('gold-catalog');
   
    Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');
   
    Route::get('/dashboard/{id}/edit', [ShopsController::class, 'edit'])->name('shop-items.edit');
    Route::get('/gold-items/shop', [ShopsController::class, 'showShopItems'])->name('gold-items.shop');
    Route::post('/gold-items/{id}/transfer', [ShopsController::class, 'transferToBranch'])->name('gold-items.transfer');
    Route::post('/gold-items/{id}/mark-as-sold', [ShopsController::class, 'markAsSold'])->name('gold-items.markAsSold');
    Route::post('/gold-items-sold/{id}/mark-as-rest', [GoldItemSoldController::class, 'markAsRest'])->name('gold-items-sold.markAsRest');
    Route::get('/gold-items-sold/{id}/edit', action: [GoldItemSoldController::class, 'edit'])->name('gold-items-sold.edit');

    // Route::get('/gold-items/{id}/outer', [OuterController::class, 'create'])->name('gold-items.outerForm');
    // Route::post('/gold-items/{id}/outer', [OuterController::class, 'storeOuter'])->name('gold-items.storeOuter');
    Route::post('/gold-items/store-outer', [ShopsController::class, 'storeOuter'])->name('gold-items.storeOuter');
    // Route::get('/gold-items/highlight', [GoldItemController::class, 'highlightUnreturned'])->name('gold-items.index');
    Route::post('gold-items/returnOuter/{serialNumber}', [ShopsController::class, 'returnOuter'])->name('gold-items.returnOuter');
    Route::post('gold-items/toggleReturn/{serial_number}', [ShopsController::class, 'toggleReturn'])->name('gold-items.toggleReturn');



    Route::get('/transfer-request/{id}/{status}', [ShopsController::class, 'handleTransferRequest'])->name('transfer.handle');
    // Route to view pending transfer requests
    Route::get('/transfer-requests', [ShopsController::class, 'viewTransferRequests'])->name('transfer.requests');
    Route::post('/gold-items/{id}/transfer', [ShopsController::class, 'transferToBranch'])
    ->name('gold-items.transfer');
    Route::get('/gold-items/{id}/transfer', [ShopsController::class, 'showTransferForm'])
    ->name('gold-items.transferForm');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/history', [OrderController::class, 'showCompletedOrders'])->name('orders.history'); 

    Route::post('/shop-items/bulk-sell', [ShopsController::class, 'BulkSell'])->name('shop-items.bulkSell');

    Route::get('/shop-items/bulk-sell-form', [ShopsController::class, 'showBulkSellForm'])->name('shop-items.bulkSellForm');
    Route::get('/shop-items/bulk-transfer-form', [ShopsController::class, 'showBulkTransferForm'])->name('shop-items.bulkTransferForm');
//     Route::post('/shop-items/bulk-sell', [ShopsController::class, 'bulkSell'])->name('shop-items.bulkSell');
// Route::post('/gold-items/bulk-transfer', [ShopsController::class, 'bulkTransfer'])->name('gold-items.bulkTransfer');
});

Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');

// Rabea Routes
Route::middleware(['auth', 'rabea'])->group(function () {
    Route::get('/orders/rabea', [RabiaController::class, 'indexForRabea'])->name('orders.rabea.index');
    Route::get('/orders/rabea/{id}', [RabiaController::class, 'show'])->name('orders.show');
    // Route::put('/orders/rabea/{id}', [RabiaController::class, 'update'])->name('orders.update');
    Route::get('/orders/requests', [RabiaController::class, 'requests'])->name('orders.requests');
    // Route::post('/orders/{order}/accept', [OrderController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/accept', [RabiaController::class, 'accept'])->name('orders.accept');

    Route::post('orders/{id}/update-status', [RabiaController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/toPrint', [RabiaController::class, 'toPrint'])->name('orders.rabea.to_print');
    Route::get('/completed-orders', [RabiaController::class, 'showCompletedOrders'])->name('completed_orders.index');
});
Route::get('/orders/rabea/{id}/edit', [RabiaController::class, 'edit'])->name('orders.rabea.edit');
Route::put('/orders/rabea/{id}', [RabiaController::class, 'update'])->name('orders.update');

// Common Routes for All Authenticated Users
Route::middleware('auth')->group(function () {
    Route::get('/gold-items-sold', [GoldItemSoldController::class, 'index'])->name('gold-items.sold');
    Route::get('/gold-pounds', [GoldPoundController::class, 'index'])->name('gold-pounds.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
});

require __DIR__.'/auth.php';