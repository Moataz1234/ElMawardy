<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Protected route that requires a token
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route for login that generates a token
Route::post('/login', [AuthController::class, 'login']);