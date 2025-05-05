<?php
require base_path('routes/api.php');

// ===================================
// Package Imports & Use Statements
// ===================================
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    NewItemController,
    Gold\GoldItemController,
    Gold\GoldItemSoldController,
    ShopsController,
    OrderController,
    GoldReportController,
    RabiaController,
    Auth\AsgardeoAuthController,
    OuterController,
    GoldCatalogController,
    Excel\ExcelImportController,
    Excel\ImportGoldItems,
    Excel\ImportSoldItems,
    Excel\ImportModels,
    Admin\GoldPriceController,
    Admin\AdminDashboardController,
    Admin\WarehouseController,
    Admin\GoldItemsAvgController,
    NotificationController,
    Admin\BarcodeController,
    ModelsController,
    SoldItemRequestController,
    AddRequestController,
    Gold\GoldPoundController,
    AddPoundsRequestController,
    TransferRequestsController,
    ItemStatisticsController,
    LaboratoryOperationController,
    LaboratoryDestinationController,
    GoldAnalysisController,
    SuperAdminRequestController,
    KasrSaleController,
    DidItemsController,
    Admin\KasrSaleAdminController,
    Admin\GoldBalanceReportController,
    Admin\Shopify\ShopifyProductController,
    Admin\OnlineModelsController,
    SerialNumberTrackingController
    // NewItemTalabatController 
};

// ===================================
// Utility Routes
// ===================================
Route::view('/loader-component', 'components.loader')->name('loader.component');
Route::get('/test-smtp', function () {
    try {
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            'smtp.bizmail.yahoo.com',
            465,
            true
        );
        $transport->setUsername('reports@elmawardy.com');
        $transport->setPassword('xwyqtxljscenmdyv');
        $transport->start();
        return "SMTP connection successful";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// ===================================
// Authentication Routes
// ===================================
Route::middleware('guest')->group(function () {
    Route::get('login', [AsgardeoAuthController::class, 'redirectToAsgardeo'])->name('login');
    Route::get('callback', [AsgardeoAuthController::class, 'handleAsgardeoCallback'])->name('auth.callback');
});

// ===================================
// Main Authenticated Routes Group
// ===================================
Route::middleware(['auth'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', function () {
        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('No authenticated user found in dashboard');
                Auth::logout();
                return redirect()->route('login');
            }

            Log::info('User accessing dashboard', [
                'user_id' => $user->id,
                'usertype' => $user->usertype,
                'email' => $user->email
            ]);

            $usertype = $user->usertype;

            if (!$usertype) {
                Log::warning('User has no usertype', ['user_id' => $user->id]);
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors('Account type not set. Please contact support.');
            }

            switch ($usertype) {
                case 'admin':
                    return redirect()->route('admin.inventory');
                case 'rabea':
                    return redirect()->route('orders.rabea.index');
                case 'Acc':
                    return redirect()->route('sell-requests.acc');
                case 'user':
                    return redirect()->route('shop-dashboard');
                default:
                    Log::error('Invalid usertype', ['usertype' => $usertype]);
                    Auth::logout();
                    return redirect()->route('login')
                        ->withErrors('Invalid account type.');
            }
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->withErrors('An error occurred. Please try again.');
        }
    })->name('dashboard');

    // ===================================
    // Accountant (ACC) Routes
    // ===================================
    Route::middleware(['auth', 'acc'])->group(function () {
        Route::get('/Acc_sell_requests', [SoldItemRequestController::class, 'viewSaleRequestsAcc'])->name('sell-requests.acc');
        // Route::post('/Acc_sell_requests/{id}/approve', [SoldItemRequestController::class, 'approveSaleRequest'])->name('sell-requests.approve');
        Route::post('/Acc_sell_requests/{id}/reject', [SoldItemRequestController::class, 'rejectSaleRequest'])->name('sell-requests.reject');
        Route::get('/all-sold-items', [SoldItemRequestController::class, 'viewAllSoldItems'])
            ->name('all-sold-items');
        Route::post('/sell-requests/bulk-approve', [SoldItemRequestController::class, 'bulkApprove'])->name('sell-requests.bulk-approve');

        // Gold Balance Report Route
        Route::get('/gold-balance-report', [GoldBalanceReportController::class, 'index'])->name('gold-balance.report');
    });

    // ===================================
    // Excel Import Routes
    // ===================================
    Route::get('/excel', [ImportGoldItems::class, 'showForm']);
    Route::post('/import-excel', [ImportGoldItems::class, 'import'])->name('import.excel');
    Route::post('/import-excel-sold', [ImportSoldItems::class, 'import'])->name('import.excel-sold');
    Route::post('/import-excel-models', [ImportModels::class, 'import'])->name('import.excel-models');

    // ===================================
    // Gold Items & Pricing Routes
    // ===================================
    Route::get('/gold-items', [ShopsController::class, 'getAllItems'])->name('gold-items.index');
    Route::get('/update_prices', [GoldPriceController::class, 'Create'])->name('gold_prices.create');
    Route::post('/update_prices/store', [GoldPriceController::class, 'store'])->name('gold_prices.store');
    Route::get('/item-details/{serial_number}', [ShopsController::class, 'getItemDetails'])->name('item.details');
    Route::post('/sell-requests/bulk-approve', [SoldItemRequestController::class, 'bulkApprove'])->name('sell-requests.bulk-approve');

    // ===================================
    // Notification Routes
    // ===================================
    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::get('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');

    // ===================================
    // Request Management Routes
    // ===================================
    Route::get('/admin/add_requests', [AddRequestController::class, 'allRequests'])->name('admin.add.requests');

    Route::get('/shop/addRequests', [AddRequestController::class, 'index'])->name('add-requests.index');
    Route::post('/shop/addRequests/accept/{id}', [AddRequestController::class, 'accept'])->name('shop.requests.accept');
    Route::post('/shop/addRequests/reject/{id}', [AddRequestController::class, 'reject'])->name('shop.requests.reject');
    Route::post('/add-requests/bulk-action', [AddRequestController::class, 'bulkAction'])->name('add-requests.bulk-action');
    Route::get('/workshop-requests', [AdminDashboardController::class, 'workshopRequests'])
        ->name('workshop.requests');
    Route::prefix('admin')->group(function () {
        Route::resource('gold_items_avg', GoldItemsAvgController::class)->names([
            'index' => 'admin.gold_items_avg.index',
            'create' => 'admin.gold_items_avg.create',
            'store' => 'admin.gold_items_avg.store',
            'edit' => 'admin.gold_items_avg.edit',
            'update' => 'admin.gold_items_avg.update',
            'destroy' => 'admin.gold_items_avg.destroy',
        ]);
    });
    // pounds
    Route::get('/gold-prices', [GoldPriceController::class, 'getGoldPrices'])->name('gold.prices');
    Route::get('/gold-pounds', [GoldPoundController::class, 'index'])->name('gold-pounds.index');
    Route::post('/gold-pounds', [GoldPoundController::class, 'store'])->name('gold-pounds.store');
    Route::get('/gold-pounds/sell-form', [GoldPoundController::class, 'showSellForm'])
        ->name('gold-pounds.sell-form');
    Route::post('/gold-pounds/create-sale-request', [GoldPoundController::class, 'createSaleRequest'])
        ->name('gold-pounds.create-sale-request');
    Route::post('gold-pounds/sell', [GoldPoundController::class, 'sell'])->name('gold-pounds.sell');

    // ===================================
    // Admin Routes
    // ===================================
    Route::middleware('admin')->group(function () {
        // Gold Item Management
        Route::post('/gold-items/add-to-session', [GoldItemController::class, 'addItemToSession'])->name('gold-items.add-to-session');
        Route::delete('/gold-items/remove-session-item', [GoldItemController::class, 'removeSessionItem'])->name('gold-items.remove-session-item');
        Route::post('/gold-items/submit-all', [GoldItemController::class, 'submitAllItems'])->name('gold-items.submit-all');

        Route::get('/gold-analysis', [GoldAnalysisController::class, 'index'])->name('gold-analysis.index');
        Route::get('/gold-analysis/export', [GoldAnalysisController::class, 'export'])->name('gold-analysis.export');

        Route::get('/sale-requests', action: [SoldItemRequestController::class, 'viewSaleRequests'])->name('sell-requests.index');
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
        // Route::post('/send-report-email', [GoldReportController::class, 'generateDailyReport'])->name('send.report.email');
        Route::post('/reports/send', [GoldReportController::class, 'sendDailyReport'])->name('reports.send');
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
    });

    // ===================================
    // Shop Routes
    // ===================================
    Route::get('/shop-items/bulk-transfer-form', [ShopsController::class, 'showBulkTransferForm'])->name('shop-items.bulkTransferForm');
    Route::post('/transfer-requests/{requestId}/handle', [ShopsController::class, 'handleTransferRequest'])
        ->name('transfer-requests.handle');
    Route::post('/gold-items/{id}/transfer-request', [ShopsController::class, 'transferRequest'])->name('gold-items.transfer-request');
    Route::get('/transfer-request/{id}/{status}', [ShopsController::class, 'handleTransferRequest'])->name('transfer.handle');
    Route::get('/transfer-requests', [ShopsController::class, 'viewTransferRequests'])->name('transfer.requests');
    // Route::get('/bulk-transfer', [ShopsController::class, 'showBulkTransferForm'])->name('gold-items.bulk-transfer-form');
    Route::post('/bulk-transfer', [ShopsController::class, 'bulkTransfer'])->name('gold-items.bulk-transfer');

    Route::middleware('user')->group(function () {
        // Shop Requests
        Route::get('/shop/requests', [ShopsController::class, 'showAdminRequests'])
            ->name('shop.requests.index');
        Route::patch('/shop/requests/{itemRequest}', [ShopsController::class, 'updateAdminRequests'])
            ->name('shop.requests.update');

        // Shop Dashboard & Items
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

        // Shop Workshop Request routes
        Route::get('/shop/workshop-requests', [DidItemsController::class, 'shopWorkshopRequests'])->name('shop.workshop.requests');
        Route::post('/shop/workshop-requests/handle', [DidItemsController::class, 'handleShopWorkshopRequests'])->name('shop.workshop.requests.handle');

        // Workshop requests for 'rabea' shop
        Route::get('/rabea/workshop-requests', [ShopsController::class, 'showWorkshopRequests'])->name('rabea.workshop.requests');
        Route::post('/rabea/workshop-requests/handle', [ShopsController::class, 'handleWorkshopRequests'])->name('rabea.workshop.requests.handle');
    });

    // ===================================
    // Rabea Routes
    // ===================================
    Route::middleware('rabea')->group(function () {
        Route::get('/orders/rabea', [RabiaController::class, 'indexForRabea'])->name('orders.rabea.index');

        Route::get('/search', [RabiaController::class, 'search'])->name('orders.search');
        Route::post('/update-status/{id}', [RabiaController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/update-status-bulk', [RabiaController::class, 'updateStatusBulk'])->name('orders.updateStatus.bulk');
        // orders show
        Route::get('/orders/rabea/{id}', [RabiaController::class, 'show'])->name('orders.show');
        // orders edit
        Route::get('/orders/edit/{id}', [RabiaController::class, 'edit'])->name('orders.rabea.edit');
        Route::put('/orders/update/{id}', [RabiaController::class, 'update'])->name('orders.update');
        // orders requests
        Route::get('/orders/requests', [RabiaController::class, 'requests'])->name('orders.requests');
        Route::post('/orders/accept', [RabiaController::class, 'accept'])->name('orders.accept');
        Route::get('/orders/toPrint', [RabiaController::class, 'toPrint'])->name('orders.rabea.to_print');
        Route::get('/orders/completed', [RabiaController::class, 'completed'])->name('orders.completed');

        // Rabea inventory management

    });

    // ===================================
    // Common Authenticated Routes
    // ===================================
    Route::post('/pound-requests/bulk-approve', [AddPoundsRequestController::class, 'bulkApprove'])
        ->name('pound-requests.bulk-approve');
    Route::post('/pound-requests/bulk-reject', [AddPoundsRequestController::class, 'bulkReject'])
        ->name('pound-requests.bulk-reject');

    // Add this route inside the authenticated routes group
    Route::get('/gold-items/same-model', [ShopsController::class, 'getItemsByModel'])
        ->name('gold-items.same-model');

    Route::resource('kasr-sales', KasrSaleController::class)->names([
        'index' => 'kasr-sales.index',
        'create' => 'kasr-sales.create',
        'store' => 'kasr-sales.store',
        'edit' => 'kasr-sales.edit',
        'update' => 'kasr-sales.update',
        'destroy' => 'kasr-sales.destroy',
    ]);
});

// ===================================
// Laboratory Operations Routes
// ===================================
Route::prefix('laboratory')->name('laboratory.')->group(function () {
    Route::resource('operations', LaboratoryOperationController::class);
    Route::post('operations/{operation}/add-output', [LaboratoryOperationController::class, 'addOutput'])
        ->name('operations.add-output');
    Route::post('operations/{operation}/add-input', [LaboratoryOperationController::class, 'addInput'])
        ->name('operations.add-input');
    Route::patch('operations/{operation}/close', [LaboratoryOperationController::class, 'closeOperation'])
        ->name('operations.close');
    Route::get('operations/{operation}/edit', [LaboratoryOperationController::class, 'edit'])
        ->name('operations.edit');
    Route::patch('/operations/{operation}/weights', [LaboratoryOperationController::class, 'updateWeights'])
        ->name('operations.update-weights');
    Route::patch('/operations/{operation}/costs', [LaboratoryOperationController::class, 'updateCosts'])
        ->name('operations.update-costs');
});

// ===================================
// SuperAdmin Routes
// ===================================
Route::prefix('Root')->group(function () {
    Route::get('/addRequests', [SuperAdminRequestController::class, 'index'])->name('superadmin.requests.index');
    Route::post('/addRequests/bulk-action', [SuperAdminRequestController::class, 'bulkAction'])->name('superadmin.requests.bulk-action');
    Route::post('/addRequests/bulk-approve-pounds', [SuperAdminRequestController::class, 'bulkApprovePounds'])->name('superadmin.requests.bulk-approve-pounds');
    Route::post('/addRequests/bulk-reject-pounds', [SuperAdminRequestController::class, 'bulkRejectPounds'])->name('superadmin.requests.bulk-reject-pounds');
});

// ===================================
// Miscellaneous Routes
// ===================================
require __DIR__ . '/auth.php';
Route::get('/export-sales', [SoldItemRequestController::class, 'exportSales'])->name('export.sales');
// rabea pounds
Route::get('/gold-items-sold', [GoldItemSoldController::class, 'index'])->name('gold-items.sold');
Route::get('/gold-pounds', [GoldPoundController::class, 'index'])->name('gold-pounds.index');
Route::get('/gold-pounds/admin', [GoldPoundController::class, 'AdminIndex'])->name('gold-pounds.admin.index');
Route::get('/gold-pounds/create', [GoldPoundController::class, 'create'])->name('gold-pounds.create');
Route::post('/gold-pounds', [GoldPoundController::class, 'store'])->name('gold-pounds.store');
//shop pounds requests
Route::get('/pound-requests', [AddPoundsRequestController::class, 'index'])->name('pound-requests.index');
Route::post('/pound-requests/bulk-approve', [AddPoundsRequestController::class, 'bulkApprove'])->name('pound-requests.bulk-approve');
Route::post('/pound-requests/bulk-reject', [AddPoundsRequestController::class, 'bulkReject'])->name('pound-requests.bulk-reject');
Route::post('/orders/store', [OrderController::class, 'store'])
    ->name('orders.store')
    ->middleware(['auth', 'web']);

Route::post('/pound-sale', [ShopsController::class, 'submitPoundPrice'])->name('pound.submit-price');

Route::post('/check-associated-pounds', [ShopsController::class, 'checkAssociatedPounds'])->name('check-associated-pounds');

Route::get('gold-pounds/search', [GoldPoundController::class, 'search'])->name('gold-pounds.search');
Route::get('gold-pounds/export', [GoldPoundController::class, 'export'])->name('gold-pounds.export');
// Route::get('gold-pounds/transfer-requests', [ShopsController::class, 'viewTransferRequestHistory'])->name('transfer.requests.admin');

// Route::get('/storage/{filename}', function ($filename) {
//     return response()->download(storage_path('app/public/' . $filename));
// })->name('download.file');

Route::get('/items-statistics', [ItemStatisticsController::class, 'index'])->name('items.statistics');
Route::get('/items-statistics/export', [ItemStatisticsController::class, 'export'])->name('items.statistics.export');

// Laboratory Operations
Route::prefix('laboratory')->name('laboratory.')->group(function () {
    Route::resource('operations', LaboratoryOperationController::class);
    Route::post('operations/{operation}/add-output', [LaboratoryOperationController::class, 'addOutput'])
        ->name('operations.add-output');
    Route::post('operations/{operation}/add-input', [LaboratoryOperationController::class, 'addInput'])
        ->name('operations.add-input');
    Route::patch('operations/{operation}/close', [LaboratoryOperationController::class, 'closeOperation'])
        ->name('operations.close');
    Route::get('operations/{operation}/edit', [LaboratoryOperationController::class, 'edit'])
        ->name('operations.edit');
    Route::patch('/operations/{operation}/weights', [LaboratoryOperationController::class, 'updateWeights'])
        ->name('operations.update-weights');
    Route::patch('/operations/{operation}/costs', [LaboratoryOperationController::class, 'updateCosts'])
        ->name('operations.update-costs');
});

Route::post('/import-sold-items/update-sources', [ImportSoldItems::class, 'updateSources'])
    ->name('import-sold-items.update-sources');

Route::post('/import-gold-items/update-sources', [ImportGoldItems::class, 'updateSources'])
    ->name('import-gold-items.update-sources');

// Workshop transfer request routes
Route::middleware(['auth'])->group(function () {
    Route::get('/workshop-requests', [DidItemsController::class, 'workshopRequests'])->name('workshop.requests.index');
    Route::post('/workshop-requests/{id}/handle', [DidItemsController::class, 'handleWorkshopRequest'])->name('workshop.requests.handle');

    Route::post('/workshop-requests/create', [DidItemsController::class, 'createWorkshopRequests'])->name('workshop.requests.create');
    Route::get('/workshop-items', [DidItemsController::class, 'workshopItems'])->name('workshop.items.index');
});

Route::post('/import-sold-items/update-prices', [ImportSoldItems::class, 'updatePrices'])
    ->name('import-sold-items.update-prices');

Route::get('/gold-pounds/transfer-form', [GoldPoundController::class, 'showBulkTransferForm'])->name('gold-pounds.transfer-form');
Route::post('/gold-pounds/bulk-transfer', [GoldPoundController::class, 'bulkTransfer'])->name('gold-pounds.bulk-transfer');

// Make sure this route exists and is named 'login'
Route::get('/login', [AsgardeoAuthController::class, 'redirectToAsgardeo'])->name('login');

Route::get('/admin/add-requests/export', [AddRequestController::class, 'export'])
    ->name('admin.add.requests.export');
Route::get('/admin/add-requests/print', [AddRequestController::class, 'printRequests'])
    ->name('admin.add.requests.print');
Route::post('/admin/add-requests/{id}/update', [AddRequestController::class, 'update'])
    ->name('admin.add.requests.update');
// Add this to your existing routes
Route::get('/shopify/orders-api-view', function () {
    return view('shopify.orders-api-view');
})->name('shopify.orders.api-view');


// Admin routes
// Route::middleware(['auth'])->group(function () {
//     // Kasr Sales Admin Routes
//     Route::get('/kasr-sales-admin', [KasrSaleAdminController::class, 'index'])->name('kasr-sales.admin.index');
//     Route::get('/admin/kasr-sales/{kasrSale}/items', [App\Http\Controllers\Admin\KasrSaleAdminController::class, 'getItems'])->name('admin.kasr-sales.items');
// });
Route::get('/get-customer-data', [ShopsController::class, 'getCustomerData']);

// Add this route to your web.php file
// Route::get('/items-list', [ShopsController::class, 'showRabeaItems'])->name('rabea.items.list');
// Route::post('/rabea/did-requests/handle', [DidItemsController::class, 'handleRabeaDIDRequests'])->name('rabea.did.requests.handle');

// Route::post('/rabea/transfer-form', [DidItemsController::class, 'bulkTransferFromRabea'])
//     ->name('gold-items.bulk-transfer');

// // Route for processing the submitted transfer form
// Route::post('/rabea/process-transfer', [DidItemsController::class, 'processRabeaTransfer'])
//     ->name('rabea.process-transfer');
// Add these routes inside your rabea middleware group in web.php
// Look for the section that starts with: Route::middleware('rabea')->group(function () {


Route::get('/rabea/items', [ShopsController::class, 'showRabeaItems'])->name('rabea.items.list');
// Route::post('/rabea/items/transfer-form', [DidItemsController::class, 'rabeaTransferForm'])->name('rabea.transfer.form');
// Route::post('/rabea/items/process-transfer', [DidItemsController::class, 'processRabeaTransfer'])->name('rabea.process.transfer');
Route::get('/rabea/get-shops', [DidItemsController::class, 'getShopsForTransfer'])->name('rabea.get.shops');
Route::post('/rabea/process-transfer', [DidItemsController::class, 'processRabeaTransfer'])->name('rabea.process.transfer');


// Workshop DID requests
Route::get('/did-requests', [DidItemsController::class, 'didRequests'])->name('rabea.did.requests');
Route::post('/did-requests/handle', [DidItemsController::class, 'handleDidRequests'])->name('rabea.did.requests.handle');

// Online Models Routes
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/online-models', [OnlineModelsController::class, 'index'])->name('online-models.index');
    Route::get('/online-models/create', [OnlineModelsController::class, 'create'])->name('online-models.create');
    Route::post('/online-models', [OnlineModelsController::class, 'store'])->name('online-models.store');
    Route::delete('/online-models/{id}', [OnlineModelsController::class, 'destroy'])->name('online-models.destroy');
    
    // Excel import routes
    Route::get('/online-models/import', [OnlineModelsController::class, 'showImportForm'])->name('online-models.import');
    Route::post('/online-models/import', [OnlineModelsController::class, 'importExcel'])->name('online-models.import.process');
    Route::post('/online-models/clear', [OnlineModelsController::class, 'clearAll'])->name('online-models.clear');
});

// Serial Number Tracking Routes
Route::get('/tracking', [SerialNumberTrackingController::class, 'index'])->name('tracking.index');
Route::get('/tracking/search', [SerialNumberTrackingController::class, 'search'])->name('tracking.search');
Route::get('/tracking/standalone', [SerialNumberTrackingController::class, 'index'])
    ->defaults('standalone', 1)
    ->name('tracking.standalone');
Route::get('/tracking/standalone/search', [SerialNumberTrackingController::class, 'search'])
    ->defaults('standalone', 1)
    ->name('tracking.standalone.search');
Route::get('/tracking/example', function() {
    return view('tracking.example');
})->name('tracking.example');
