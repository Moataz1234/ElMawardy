<?php

// Include route files
require base_path('routes/api.php');
require base_path('routes/admin.php');
require base_path('routes/shop.php');
require base_path('routes/rabea.php');
require base_path('routes/acc.php');
require base_path('routes/super.php');

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
    SerialNumberTrackingController,
    GoldItemWeightHistoryController,
    ForProductionController
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
Route::get('/update_prices', [GoldPriceController::class, 'Create'])->name('gold_prices.create');
Route::post('/update_prices/store', [GoldPriceController::class, 'store'])->name('gold_prices.store');

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
                case 'super':
                    return redirect()->route('super.dashboard');
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
    // MOVED TO routes/acc.php

    Route::get('/gold-balance-report', [GoldBalanceReportController::class, 'index'])->name('gold-balance.report');

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

        // Production Management Routes
        Route::resource('production', ForProductionController::class)->names([
            'index' => 'production.index',
            'create' => 'production.create',
            'store' => 'production.store',
            'edit' => 'production.edit',
            'update' => 'production.update',
            'destroy' => 'production.destroy',
        ]);
        Route::get('production/model-status', [ForProductionController::class, 'getModelStatus'])->name('production.model-status');
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
    // MOVED to routes/admin.php

    // ===================================
    // Super User Routes
    // ===================================
    // MOVED to routes/super.php

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

    // MOVED to routes/shop.php
    
    // ===================================
    // Rabea Routes
    // ===================================
    // MOVED to routes/rabea.php

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

    // Gold Item Weight History Routes
    Route::get('/gold-item-weight-history', [GoldItemWeightHistoryController::class, 'index'])
        ->name('gold-item-weight-history.index');
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
