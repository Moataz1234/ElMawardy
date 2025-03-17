<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AsgardeoAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RabiaController;
use App\Http\Controllers\Api\RabiaApiController;
use App\Http\Controllers\Api\ModelsController;
use App\Http\Controllers\Api\ShopifyCustomersController;
use App\Http\Controllers\Api\GoldItemsController;

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::get('login-url', [AsgardeoAuthController::class, 'getLoginUrl']);
    Route::get('callback', [AsgardeoAuthController::class, 'handleCallback']);
    Route::post('logout', [AsgardeoAuthController::class, 'logout']);
});

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
// });

// Your existing pound-sale route

Route::get('/example', function () {
    return response()->json(['message' => 'This is an example API route.']);
});

// Example API route

Route::middleware(['auth'])->group(function () {
    Route::prefix('rabea')->group(function () {
        Route::get('/orders', [RabiaApiController::class, 'getOrders']);
        Route::get('/orders/to-print', [RabiaApiController::class, 'getToPrintOrders']);
        Route::get('/orders/completed', [RabiaApiController::class, 'getCompletedOrders']);
        Route::post('/orders/update-status-bulk', [RabiaApiController::class, 'updateStatusBulk']);
        Route::get('/orders/{id}', [RabiaApiController::class, 'getOrder']);
        Route::put('/orders/{id}', [RabiaApiController::class, 'updateOrder']);
    });

 
});

// Models API Routes
Route::apiResource('models', ModelsController::class)->names([
    'index' => 'api.models.index',
    'store' => 'api.models.store',
    'show' => 'api.models.show',
    'update' => 'api.models.update',
    'destroy' => 'api.models.destroy',
]);
Route::prefix('shopify')->group(function () {
    // Customers Routes
    Route::get('/customers', [ShopifyCustomersController::class, 'index']);
    Route::get('/customers/search', [ShopifyCustomersController::class, 'search']);
    Route::get('/customers/all', [ShopifyCustomersController::class, 'getAllCustomers']);
    Route::get('/customers/page/{page}', [ShopifyCustomersController::class, 'getCustomersByPage']);
    Route::get('/customers/{customerId}', [ShopifyCustomersController::class, 'show']);
});

// Gold Items API Routes
Route::apiResource('gold-items', GoldItemsController::class)->names([
    'index' => 'api.gold-items.index',
    'store' => 'api.gold-items.store',
    'show' => 'api.gold-items.show',
    'update' => 'api.gold-items.update',
    'destroy' => 'api.gold-items.destroy',
]);
