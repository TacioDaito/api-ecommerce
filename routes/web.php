
<?php
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::name('login')->group(function () {
        Route::get('login', [LoginController::class, 'view']);
        Route::post('login', [LoginController::class, 'login']);
    });
    Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');
});
