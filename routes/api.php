<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    Route::get('user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
        ]);
    });
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('products', ProductController::class)->middleware('can:onlyAllowAdmin');
});


