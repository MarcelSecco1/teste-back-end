<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::resource('/user', UserController::class);
Route::resource('/transaction', TransactionController::class);
Route::resource('/balance', BalanceController::class);
