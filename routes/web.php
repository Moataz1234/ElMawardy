<?php

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
    ModelsController,
    SoldItemRequestController,
    AddRequestController
};

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

    Route::get('/notifications/stream', [NotificationController::class, 'stream'])->name('notifications.stream');
    Route::get('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');

    Route::get('/gold-items', [GoldItemController::class, 'index'])->name('gold-items.index');
    Route::get('/gold-items/create', [GoldItemController::class, 'create'])->name('gold-items.create');
    Route::post('/gold-items/add-to-session', [GoldItemController::class, 'addItemToSession'])->name('gold-items.add-to-session');
    Route::delete('/gold-items/remove-session-item', [GoldItemController::class, 'removeSessionItem'])->name('gold-items.remove-session-item');
    Route::post('/gold-items/submit-all', [GoldItemController::class, 'submitAllItems'])->name('gold-items.submit-all');
});

require __DIR__ . '/auth.php';
