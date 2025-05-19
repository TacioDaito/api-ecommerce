<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::middleware('auth:api')->group(function () {
    Route::get('user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
        ]);
    });
    Route::apiResource('orders', OrderController::class);
});


