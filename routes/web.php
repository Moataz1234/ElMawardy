<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewItemController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


route::get('/admin/dashboard',[HomeController::class,'index'])->middleware(['auth','admin'])->name('admin-dashboard');
//   for new item form
Route::middleware('admin')->group(function (){
Route::get('/new-item', [NewItemController::class, 'create'])->name('new-item.create');
Route::post('/new-item', [NewItemController::class, 'store'])->name('new-item.store');
Route::get('/search-model', [HomeController::class, 'searchModel'])->name('search.model');

});



// when user logged out
// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', [HomeController::class, 'checked']);
//     // Other protected routes
// });