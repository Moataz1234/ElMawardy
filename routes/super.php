<?php

use Illuminate\Support\Facades\Route;

Route::middleware('super')->prefix('super')->name('super.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Super\SuperDashboardController::class, 'index'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [App\Http\Controllers\Super\SuperDashboardController::class, 'users'])->name('users');
    Route::get('/users/{id}/edit', [App\Http\Controllers\Super\SuperDashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'deleteUser'])->name('users.delete');
    
    // Shops Management
    Route::get('/shops', [App\Http\Controllers\Super\SuperDashboardController::class, 'shops'])->name('shops');
    Route::get('/shops/{id}/edit', [App\Http\Controllers\Super\SuperDashboardController::class, 'editShop'])->name('shops.edit');
    Route::put('/shops/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'updateShop'])->name('shops.update');
    
    // Gold Items Management
    Route::get('/gold-items', [App\Http\Controllers\Super\SuperDashboardController::class, 'goldItems'])->name('gold-items');
    Route::get('/sold-items', [App\Http\Controllers\Super\SuperDashboardController::class, 'soldItems'])->name('sold-items');
    
    // Models Management
    Route::get('/models', [App\Http\Controllers\Super\SuperDashboardController::class, 'models'])->name('models.index');
    Route::get('/models/create', [App\Http\Controllers\Super\SuperModelsController::class, 'create'])->name('models.create');
    Route::post('/models', [App\Http\Controllers\Super\SuperModelsController::class, 'store'])->name('models.store');
    Route::get('/models/{id}', [App\Http\Controllers\Super\SuperModelsController::class, 'show'])->name('models.show');
    Route::get('/models/{id}/edit', [App\Http\Controllers\Super\SuperModelsController::class, 'edit'])->name('models.edit');
    Route::put('/models/{id}', [App\Http\Controllers\Super\SuperModelsController::class, 'update'])->name('models.update');
    Route::delete('/models/{id}', [App\Http\Controllers\Super\SuperModelsController::class, 'destroy'])->name('models.destroy');
    
    // Customer Management
    Route::get('/customers', [App\Http\Controllers\Super\SuperDashboardController::class, 'customers'])->name('customers');
    Route::get('/customers/create', [App\Http\Controllers\Super\SuperDashboardController::class, 'createCustomer'])->name('customers.create');
    Route::post('/customers', [App\Http\Controllers\Super\SuperDashboardController::class, 'storeCustomer'])->name('customers.store');
    Route::get('/customers/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'showCustomer'])->name('customers.show');
    Route::get('/customers/{id}/edit', [App\Http\Controllers\Super\SuperDashboardController::class, 'editCustomer'])->name('customers.edit');
    Route::put('/customers/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'updateCustomer'])->name('customers.update');
    Route::delete('/customers/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'deleteCustomer'])->name('customers.delete');
    
    // Requests Management
    Route::get('/requests', [App\Http\Controllers\Super\SuperDashboardController::class, 'requests'])->name('requests');
    Route::post('/requests/{type}/{id}/handle', [App\Http\Controllers\Super\SuperDashboardController::class, 'handleRequest'])->name('handle-request');
    
    // Orders Management
    Route::get('/orders', [App\Http\Controllers\Super\SuperDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{id}/edit', [App\Http\Controllers\Super\SuperDashboardController::class, 'editOrder'])->name('orders.edit');
    Route::put('/orders/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'updateOrder'])->name('orders.update');
    Route::delete('/orders/{id}', [App\Http\Controllers\Super\SuperDashboardController::class, 'deleteOrder'])->name('orders.delete');
    
    // Kasr Sales Management
    Route::get('/kasr-sales', [App\Http\Controllers\Super\SuperDashboardController::class, 'kasrSales'])->name('kasr-sales');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Super\SuperDashboardController::class, 'analytics'])->name('analytics');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Super\SuperDashboardController::class, 'settings'])->name('settings');
}); 