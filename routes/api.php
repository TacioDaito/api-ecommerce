<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    Route::get('user', function () {
        return response()->json([
            'user' => Auth::user(),
        ]);
    });
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('products', ProductController::class)->middleware('can:onlyAllowAdmin');
});
