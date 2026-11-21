<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicOrderController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Staff\ReportController as StaffReportController;
use App\Http\Controllers\Staff\ServiceController as StaffServiceController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    try {
        DB::select('SELECT 1');

        return response()->json([
            'status' => 'ok',
            'database' => 'ok',
        ], 200);
    } catch (Throwable) {
        return response()->json([
            'status' => 'error',
            'database' => 'unavailable',
        ], 503);
    }
})->withoutMiddleware('web');

// Public customer endpoints
Route::get('/services', [PublicOrderController::class, 'services'])->name('services.public');
Route::post('/orders', [PublicOrderController::class, 'store'])->name('orders.store');
Route::get('/track/{orderCode}', [PublicOrderController::class, 'track'])->name('orders.track');

// Staff authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Protected staff & admin endpoints
Route::prefix('staff')
    ->middleware('auth')
    ->name('staff.')
    ->group(function (): void {
        Route::get('/orders', [StaffOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [StaffOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [StaffOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/weigh', [StaffOrderController::class, 'weigh'])->name('orders.weigh');
        Route::patch('/orders/{order}/payment', [StaffOrderController::class, 'updatePayment'])->name('orders.payment');

        Route::get('/services', [StaffServiceController::class, 'index'])->name('services.index');
        Route::post('/services', [StaffServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}', [StaffServiceController::class, 'show'])->name('services.show');
        Route::match(['put', 'patch'], '/services/{service}', [StaffServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [StaffServiceController::class, 'destroy'])->name('services.destroy');

        Route::get('/reports/daily-revenue', [StaffReportController::class, 'dailyRevenue'])->name('reports.daily-revenue');
    });
