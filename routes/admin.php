<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminShopController;
use App\Http\Controllers\Gold\GoldItemController;
use App\Http\Controllers\GoldAnalysisController;
use App\Http\Controllers\SoldItemRequestController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GoldReportController;
use App\Http\Controllers\NewItemController;
use App\Http\Controllers\TransferRequestsController;
use App\Http\Controllers\Admin\Shopify\ShopifyProductController;
use App\Http\Controllers\Admin\KasrSaleAdminController;
use App\Http\Controllers\KasrSaleController;
use App\Http\Controllers\Gold\GoldItemSoldController;
use App\Http\Controllers\ForProductionController;

Route::middleware(['auth', 'admin'])->group(function () {
    // Gold Items Management from admin.php
    Route::get('/gold-items', [AdminShopController::class, 'getAllItems'])->name('gold-items.index');
    Route::get('/item-details/{serial_number}', [AdminShopController::class, 'getItemDetails'])->name('item.details');
    // This route name 'gold-items.same-model' is also defined in web.php for general auth users.
    // The one here will be specific to admin.
    Route::get('/gold-items/same-model', [AdminShopController::class, 'getItemsByModel'])->name('admin.gold-items.same-model');

    // Transfer Management from admin.php
    // The name 'transfer-requests.handle' is defined multiple times. Let's make it specific.
    Route::post('/transfer-requests/{requestId}/handle', [AdminShopController::class, 'handleTransferRequest'])
        ->name('admin.transfer-requests.handle');

    // Pound Management from admin.php
    Route::post('/pound-sale', [AdminShopController::class, 'submitPoundPrice'])->name('pound.submit-price');
    Route::post('/check-associated-pounds', [AdminShopController::class, 'checkAssociatedPounds'])
        ->name('check-associated-pounds');

    // Gold Item Management from web.php
    Route::post('/gold-items/add-to-session', [GoldItemController::class, 'addItemToSession'])->name('gold-items.add-to-session');
    Route::delete('/gold-items/remove-session-item', [GoldItemController::class, 'removeSessionItem'])->name('gold-items.remove-session-item');
    Route::post('/gold-items/submit-all', [GoldItemController::class, 'submitAllItems'])->name('gold-items.submit-all');

    Route::get('/gold-analysis', [GoldAnalysisController::class, 'index'])->name('gold-analysis.index');
    Route::get('/gold-analysis/export', [GoldAnalysisController::class, 'export'])->name('gold-analysis.export');

    Route::get('/sale-requests', [SoldItemRequestController::class, 'viewSaleRequests'])->name('sell-requests.index');
    Route::get('/all-sale-requests', [SoldItemRequestController::class, 'viewAllSaleRequests'])->name('sale-requests.all');


    Route::get('/generate-model', [ModelsController::class, 'generateModel']);

    Route::get('/gold-items/create', [GoldItemController::class, 'create'])->name('gold-items.create');
    Route::resource('models', ModelsController::class)->names([
        'index' => 'models.index',
        // 'create' => 'models.create',
        'store' => 'models.store',
        'edit' => 'models.edit',
        'update' => 'models.update',
        'destroy' => 'models.destroy',
    ]);
    Route::get('/models/create', [ModelsController::class, 'create'])->name('models.create');

    Route::get('/check-model-exists', [ModelsController::class, 'checkModelExists']);
    Route::post('/gold-items/store', [GoldItemController::class, 'store'])->name('gold-items.store');
    Route::get('/gold-items/{id}/edit', [GoldItemController::class, 'edit'])->name('gold-items.edit');
    Route::put('/gold-items/{id}', [AdminDashboardController::class, 'update'])->name('gold-items.update');
    Route::get('/gold-items/model-details', [GoldItemController::class, 'getModelDetails']);
    Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.view');
    Route::get('/barcode/export', [BarcodeController::class, 'export'])->name('barcode.export');
    Route::get('/admin/barcode/export-barcode', [BarcodeController::class, 'exportBarcode'])->name('barcode.exportBarcode');
    Route::get('/gold-items/{id}/export-barcode', [BarcodeController::class, 'exportSingleItemBarcode'])
        ->name('item.export.barcode');
    Route::get('/generate-qr', [BarcodeController::class, 'generate'])->name('barcode.generate');

    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('admin.warehouse.index');
    Route::post('/warehouse', [WarehouseController::class, 'store'])->name('admin.warehouse.store');
    Route::post('/bulk-action', [WarehouseController::class, 'bulkAction'])->name('warehouse.bulkAction');
    Route::get('/warehouse/{id}/edit', [WarehouseController::class, 'edit'])->name('admin.warehouse.edit');
    Route::post('/warehouse/{id}/assign', [WarehouseController::class, 'assignToShop'])
        ->name('admin.warehouse.assign');

    // Route::get('/talabat/model-details', [TalabatController::class, 'getTalabatDetails']);
    Route::get('/admin/new-dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/inventory', [AdminDashboardController::class, 'index'])->name('admin.inventory');
    Route::post('/admin/inventory/bulk-action', [AdminDashboardController::class, 'bulkAction'])
        ->name('bulk-action')
        ->middleware('web');
    Route::get('/deleted-items-history', [AdminDashboardController::class, 'deletedItems'])
        ->name('deleted-items.history');
    // Route::get('/workshop-items', [AdminDashboardController::class, 'workshopItems'])
    //     ->name('workshop.items');

    Route::post('/admin/workshop/transfer-requests', [AdminDashboardController::class, 'createWorkshopRequests'])
        ->name('workshop.requests.create');
    Route::post('/workshop-requests/{id}/handle', [AdminDashboardController::class, 'handleWorkshopRequest'])
        ->name('workshop.requests.handle');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::get('/admin/sold-items', [AdminDashboardController::class, 'Sold'])->name('admin.sold-items');
    // Route::get('/daily-report', [GoldReportController::class, 'generateDailyReport'])->name('daily.report');
    Route::get('/daily-report/pdf', [GoldReportController::class, 'generateDailyReport'])->name('daily.report.pdf');
    Route::get('/reports', [GoldItemSoldController::class, 'viewReports'])->name('reports.view');
    Route::post('/reports/send', [GoldReportController::class, 'sendDailyReport'])->name('reports.send');
    // New Item Routes
    Route::get('/new-item/create', [NewItemController::class, 'create'])->name('new-item.create');
    Route::post('/new-item/store', [NewItemController::class, 'store'])->name('new-item.store');
    Route::get('/transfer-requests/history', [TransferRequestsController::class, 'viewTransferRequestHistory'])
        ->name('transfer.requests.admin');


    // Shopify Routes
    Route::prefix('shopify')->name('shopify.')->group(function () {
        // Product routes
        Route::get('/products', [ShopifyProductController::class, 'index'])->name('products');
        Route::get('/products/edit/{product_id}', [ShopifyProductController::class, 'showEditImageForm'])->name('products.showEditImageForm');
        Route::post('/products/edit/{product_id}', [ShopifyProductController::class, 'editProduct'])->name('products.editProduct');
        Route::post('/update-from-excel', [ShopifyProductController::class, 'updateFromExcel'])->name('updateFromExcel');
        // Route::get('/products/update-g-inventory', [ShopifyProductController::class, 'updateGInventory'])->name('updateGInventory');
        // Route::get('/products/update-g-inventory-location', [ShopifyProductController::class, 'updateGInventoryAtLocation'])->name('updateGInventoryAtLocation');
        Route::get('/products/update-all-zero-inventory', [ShopifyProductController::class, 'updateAllZeroInventory'])->name('updateAllZeroInventory');
        Route::post('/products/import-skus-set-zero', [ShopifyProductController::class, 'importSkusSetZero'])->name('importSkusSetZero');
        // Order routes
        Route::get('/orders', [App\Http\Controllers\Admin\Shopify\ShopifyOrderController::class, 'index'])->name('orders');
        Route::post('/orders/{id}/fulfill', [App\Http\Controllers\Admin\Shopify\ShopifyOrderController::class, 'fulfillOrder'])->name('orders.fulfill');
        Route::post('/orders/{id}/paid', [App\Http\Controllers\Admin\Shopify\ShopifyOrderController::class, 'markAsPaid'])->name('orders.markAsPaid');
        Route::get('/orders/{orderId}/pdf', [App\Http\Controllers\Admin\Shopify\ShopifyOrderController::class, 'generatePdf'])->name('orders.pdf');

        // Checkout routes
        Route::get('/abandoned-checkouts', [App\Http\Controllers\Admin\Shopify\ShopifyCheckoutController::class, 'index'])->name('abandonedCheckouts');

        // Price update routes
        Route::get('/update-prices', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'seeUpdatePrice'])->name('updatePrices');
        Route::post('/update-gold-prices', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'updateGoldPrices'])->name('updateGold');
        Route::post('/update-diamond-prices', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'updateDiamondPrices'])->name('updateDiamond');
        Route::post('/update-prices-from-csv', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'updatePricesFromCsv'])->name('updatePricesFromCsv');
        Route::post('/update-specific-products', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'updateSpecificProducts'])->name('updateSpecificProducts');
        Route::post('/add-products-to-collection', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'addProductsToCollection'])->name('addProductsToCollection');
        Route::post('/update-quantity-to-one', [App\Http\Controllers\Admin\Shopify\ShopifyPriceController::class, 'updateQuantityToOne'])->name('updateQuantityToOne');
    });

    // Kasr Sales Admin Routes
    Route::get('/kasr-sales-admin', [KasrSaleAdminController::class, 'index'])->name('kasr-sales.admin.index');
    Route::get('/admin/kasr-sales/{kasrSale}/items', [KasrSaleAdminController::class, 'getItems'])->name('admin.kasr-sales.items');
    Route::post('/kasr-sales/batch-update', [KasrSaleAdminController::class, 'batchUpdate'])->name('kasr-sales.batch-update');
    Route::get('/kasr-sales-completed', [KasrSaleController::class, 'completed'])->name('kasr-sales.completed');

    // Shop name matching tool
    Route::post('/update-shop-name', [AdminDashboardController::class, 'updateShopName'])->name('admin.update-shop-name');

    // Production Management Routes
    Route::get('/production', [ForProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create', [ForProductionController::class, 'create'])->name('production.create');
    Route::post('/production', [ForProductionController::class, 'store'])->name('production.store');
    Route::get('/production/{production}/edit', [ForProductionController::class, 'edit'])->name('production.edit');
    Route::put('/production/{production}', [ForProductionController::class, 'update'])->name('production.update');
    Route::delete('/production/{production}', [ForProductionController::class, 'destroy'])->name('production.destroy');
    Route::get('/production/import', [ForProductionController::class, 'showImport'])->name('production.import.show');
    Route::post('/production/import', [ForProductionController::class, 'import'])->name('production.import');
    Route::get('/production/template', [ForProductionController::class, 'downloadTemplate'])->name('production.template');
    Route::get('/production/status', [ForProductionController::class, 'getModelStatus'])->name('production.status');
    Route::get('/production/model-details', [ForProductionController::class, 'getModelDetails'])->name('production.model-details');
}); 