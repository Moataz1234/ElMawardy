<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rabea\RabeaShopController;
use App\Http\Controllers\RabiaController;

Route::middleware(['auth', 'rabea'])->group(function () {
    // Workshop Requests
    Route::get('/workshop-requests', [RabeaShopController::class, 'showWorkshopRequests'])
        ->name('rabea.workshop.requests');
    Route::post('/workshop-requests/handle', [RabeaShopController::class, 'handleWorkshopRequests'])
        ->name('rabea.workshop.requests.handle');
    
    // Rabea Items
    // The name rabea.items.list is also in web.php, but this one is specific to rabea middleware
    Route::get('/items', [RabeaShopController::class, 'showRabeaItems'])
        ->name('rabea.items.list.rabea');

    // From web.php
    Route::get('/orders/rabea', [RabiaController::class, 'indexForRabea'])->name('orders.rabea.index');
    Route::get('/search', [RabiaController::class, 'search'])->name('orders.search');
    Route::post('/update-status/{id}', [RabiaController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/update-status-bulk', [RabiaController::class, 'updateStatusBulk'])->name('orders.updateStatus.bulk');
    Route::get('/orders/rabea/{id}', [RabiaController::class, 'show'])->name('orders.show');
    Route::get('/orders/edit/{id}', [RabiaController::class, 'edit'])->name('orders.rabea.edit');
    Route::put('/orders/update/{id}', [RabiaController::class, 'update'])->name('orders.update');
    Route::get('/orders/requests', [RabiaController::class, 'requests'])->name('orders.requests');
    Route::post('/orders/accept', [RabiaController::class, 'accept'])->name('orders.accept');
    Route::get('/orders/toPrint', [RabiaController::class, 'toPrint'])->name('orders.rabea.to_print');
    Route::get('/orders/completed', [RabiaController::class, 'completed'])->name('orders.completed');
}); 