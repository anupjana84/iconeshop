<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    require __DIR__ . '/admin/orders.php';
    require __DIR__ . '/admin/users_customers.php';
    require __DIR__ . '/admin/products_catalog.php';
    require __DIR__ . '/admin/sales_purchases.php';
    require __DIR__ . '/admin/finance.php';
    require __DIR__ . '/admin/services.php';
    require __DIR__ . '/admin/reports_notifications.php';
});
