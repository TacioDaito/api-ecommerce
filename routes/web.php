
<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::name('login')->group(function () {
    Route::get('login', [LoginController::class, 'view']);
    Route::post('login', [LoginController::class, 'login']);
});