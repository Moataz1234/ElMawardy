<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoldItemRequestController;
use App\Http\Controllers\Admin\GoldBalanceReportController;

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