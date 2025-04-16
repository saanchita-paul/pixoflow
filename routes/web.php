<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\OrderBrowserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/create-user', [RegisteredUserController::class, 'create'])->name('admin.create-user');
    Route::post('/admin/create-user', [RegisteredUserController::class, 'store'])->name('admin.store-user');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/admin/order-progress/{order}', [AdminController::class, 'orderProgress'])->name('admin.order-progress');
    Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
    Route::get('/progress-dashboard', [DashboardController::class, 'index'])->name('progress.dashboard');
});
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/{order}/claim-files', [OrderController::class, 'claimFiles'])->name('orders.claim-files');
Route::post('/claims/{claim}/status', [OrderController::class, 'updateStatus'])->name('claims.update-status');




require __DIR__.'/auth.php';
