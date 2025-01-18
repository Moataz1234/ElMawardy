<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\{
    TestController,
    HomeController,
    ProfileController,
    NewItemController,
    Gold\GoldItemController,
    Gold\GoldItemSoldController,
    Gold\GoldPoundController,
    ShopsController,
    OrderController,
    Admin\ShopifyProductController,
    GoldReportController,
    RabiaController,
    Auth\AsgardeoAuthController,
    OuterController,
    GoldCatalogController,
    ExcelImportController,
    Excel\ImportGoldItems,
    Excel\ImportSoldItems,
    Excel\ImportModels,
    Admin\GoldPriceController,
    Admin\AdminDashboardController,
    Admin\WarehouseController,
    Admin\GoldItemsAvgController,
    NotificationController,
    Admin\BarcodeController,
    ModelsController
    // NewItemTalabatController
};

// Test SMTP Route
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

Route::middleware('guest')->group(function () {
    Route::get('login', [AsgardeoAuthController::class, 'redirectToAsgardeo'])->name('login');
    Route::get('callback', [AsgardeoAuthController::class, 'handleAsgardeoCallback'])->name('auth.callback');
});
// Authenticated Routes
Route::middleware(['auth'])->group(function () {
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
                case 'user':
                    return redirect()->route('shop-dashboard');
                    // return view('dashboard');
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

    // Import Excels
    Route::get('/excel', [ImportGoldItems::class, 'showForm']);
    Route::post('/import-excel', [ImportGoldItems::class, 'import'])->name('import.excel');
    Route::post('/import-excel-sold', [ImportSoldItems::class, 'import'])->name('import.excel-sold');
    Route::post('/import-excel-models', [ImportModels::class, 'import'])->name('import.excel-models');
    Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');
    Route::get('/update_prices', [GoldPriceController::class, 'Create'])->name('gold_prices.create');
    Route::post('/update_prices/store', [GoldPriceController::class, 'store'])->name('gold_prices.store');
    Route::get('/gold-items/same-model', [ShopsController::class, 'getItemsByModel']);

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
    // Admin Routes
    Route::middleware('admin')->group(function () {
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
        Route::get('/gold-items/model-details', [ModelsController::class, 'getModelDetails']);
        Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.view');
        Route::get('/barcode/export', [BarcodeController::class, 'export'])->name('barcode.export');

        Route::put('/gold-items-sold/{id}', [GoldItemSoldController::class, 'update'])->name('gold-items-sold.update');
        Route::get('/warehouse', [WarehouseController::class, 'index'])->name('admin.warehouse.index');
        Route::post('/warehouse', [WarehouseController::class, 'store'])->name('admin.warehouse.store');
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
        Route::get('/workshop-items', [AdminDashboardController::class, 'workshopItems'])
            ->name('workshop.items');

        Route::post('/admin/workshop/transfer-requests', [AdminDashboardController::class, 'createWorkshopRequests'])
            ->name('workshop.requests.create');
        Route::post('/workshop-requests/{id}/handle', [AdminDashboardController::class, 'handleWorkshopRequest'])
            ->name('workshop.requests.handle');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.mark-as-read');
        Route::get('/admin/sold-items', [AdminDashboardController::class, 'Sold'])->name('admin.sold-items');
        Route::get('/daily-report', [GoldReportController::class, 'generateDailyReport'])->name('daily.report');
        Route::get('/daily-report/pdf', [GoldReportController::class, 'generateDailyReport'])->name('daily.report.pdf');
        Route::post('/send-report-email', [GoldReportController::class, 'generateDailyReport'])->name('send.report.email');
        Route::get('/reports', [GoldItemSoldController::class, 'viewReports'])->name('reports.view');
        Route::get('/new-item/create', [NewItemController::class, 'create'])->name('new-item.create');
        Route::post('/new-item/store', [NewItemController::class, 'store'])->name('new-item.store');
        Route::get('/transfer-requests/history', [ShopsController::class, 'viewTransferRequestHistory'])->name('transfer.requests.history');
        Route::get('/shopify-products', [ShopifyProductController::class, 'index'])->name('shopify.products');
        Route::get('/shopify-products/orders', [ShopifyProductController::class, 'Order_index'])->name('orders_shopify');
        Route::post('/shopify/orders/{id}/fulfill', [ShopifyProductController::class, 'fulfillOrder'])->name('fulfill_order');
        Route::post('/shopify/orders/{id}/paid', [ShopifyProductController::class, 'markAsPaid'])->name('mark_as_paid');
        Route::get('/orders/{orderId}/pdf', [ShopifyProductController::class, 'generatePDF'])->name('order.pdf');
        Route::get('/shopify-products/abandoned-checkouts', [ShopifyProductController::class, 'AbandonedCheckouts_index'])->name('abandoned_checkouts_shopify');
        Route::get('/shopify-products/edit/{product_id}', [ShopifyProductController::class, 'showEditImageForm'])->name('shopify.products.showEditImageForm');
        Route::post('/shopify-products/edit/{product_id}', [ShopifyProductController::class, 'editProduct'])->name('shopify.products.editProduct');
        Route::post('/update-prices', [ShopifyProductController::class, 'updatePricesFromCsv']);
    });

    // Shop Routes
    Route::middleware('user')->group(function () {
        Route::get('/shop/requests', [ShopsController::class, 'showAdminRequests'])
            ->name('shop.requests.index');
        Route::patch('/shop/requests/{itemRequest}', [ShopsController::class, 'updateAdminRequests'])
            ->name('shop.requests.update');
        Route::get('/shop/dashboard', [ShopsController::class, 'showShopItems'])->name('shop-dashboard');
        Route::get('/gold-catalog', [GoldCatalogController::class, 'ThreeView'])->name('gold-catalog');
        Route::get('/dashboard/{id}/edit', [ShopsController::class, 'edit'])->name('shop-items.edit');
        Route::get('/gold-items/shop', [ShopsController::class, 'showShopItems'])->name('gold-items.shop');
        Route::post('/gold-items-sold/{id}/mark-as-rest', [GoldItemSoldController::class, 'markAsRest'])->name('gold-items-sold.markAsRest');
        Route::get('/gold-items-sold/{id}/edit', [GoldItemSoldController::class, 'edit'])->name('gold-items-sold.edit');
        Route::post('/gold-items/store-outer', [ShopsController::class, 'storeOuter'])->name('gold-items.storeOuter');
        Route::post('gold-items/returnOuter/{serialNumber}', [ShopsController::class, 'returnOuter'])->name('gold-items.returnOuter');
        Route::post('gold-items/toggleReturn/{serial_number}', [ShopsController::class, 'toggleReturn'])->name('gold-items.toggleReturn');
        Route::post('/gold-items/{id}/transfer-request', [ShopsController::class, 'transferRequest'])->name('gold-items.transfer-request');
        Route::get('/transfer-request/{id}/{status}', [ShopsController::class, 'handleTransferRequest'])->name('transfer.handle');
        Route::get('/transfer-requests', [ShopsController::class, 'viewTransferRequests'])->name('transfer.requests');
        Route::get('/gold-items/{id}/transfer', [ShopsController::class, 'showTransferForm'])->name('gold-items.transferForm');
        Route::get('/bulk-transfer', [ShopsController::class, 'showBulkTransferForm'])->name('gold-items.bulk-transfer-form');
        Route::post('/bulk-transfer', [ShopsController::class, 'bulkTransfer'])->name('gold-items.bulk-transfer');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/history', [OrderController::class, 'showCompletedOrders'])->name('orders.history');
        Route::post('/shop-items/bulk-sell', [ShopsController::class, 'BulkSell'])->name('shop-items.bulkSell');
        Route::get('/shop-items/bulk-sell-form', [ShopsController::class, 'showBulkSellForm'])->name('shop-items.bulkSellForm');
        Route::get('/shop-items/bulk-transfer-form', [ShopsController::class, 'showBulkTransferForm'])->name('shop-items.bulkTransferForm');
        Route::get('/import', [ExcelImportController::class, 'showForm'])->name('import.form');
        Route::post('/import', [ExcelImportController::class, 'import'])->name('excel.import');
    });

    // Rabea Routes
    Route::middleware('rabea')->group(function () {
        Route::get('/orders/rabea', [RabiaController::class, 'indexForRabea'])->name('orders.rabea.index');
        Route::get('/search', [RabiaController::class, 'search'])->name('orders.search');
        Route::post('/update-status/{id}', [RabiaController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/update-status-bulk', [RabiaController::class, 'updateStatusBulk'])->name('orders.updateStatus.bulk');
        Route::get('/orders/rabea/{id}', [RabiaController::class, 'show'])->name('orders.show');
        Route::get('/orders/requests', [RabiaController::class, 'requests'])->name('orders.requests');
        Route::post('/orders/accept', [RabiaController::class, 'accept'])->name('orders.accept');
        Route::get('/orders/toPrint', [RabiaController::class, 'toPrint'])->name('orders.rabea.to_print');
        Route::get('/orders/completed', [RabiaController::class, 'completed'])->name('orders.completed');
    });

    // Common Routes for All Authenticated Users
    Route::get('/gold-items-sold', [GoldItemSoldController::class, 'index'])->name('gold-items.sold');
    Route::get('/gold-pounds', [GoldPoundController::class, 'index'])->name('gold-pounds.index');
});

require __DIR__ . '/auth.php';
