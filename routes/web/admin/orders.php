<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('order')->group(function () {
    Route::get('/', [OrderController::class, 'orderView'])->name('order.view');
    Route::get('pending', [OrderController::class, 'pendingOrders'])->name('order.pending');
    Route::get('delivered', [OrderController::class, 'deliveredOrders'])->name('order.delivered');
    Route::get('canceled', [OrderController::class, 'canceledOrders'])->name('order.canceled');
    Route::get('cash', [OrderController::class, 'cashOrders'])->name('order.cash');
    Route::get('process/{id}', [OrderController::class, 'orderProcess'])->name('order.process');
    Route::get('direct/process/{id}', [OrderController::class, 'directOrderProcess'])->name('order.direct.process');
    Route::post('place/{id}', [OrderController::class, 'orderPlace'])->name('order.place');
    Route::post('direct/place/{id}', [OrderController::class, 'directOrderPlace'])->name('order.direct.place');
    Route::put('cancel/{id}', [OrderController::class, 'orderCancel'])->name('order.cancel');
    Route::get('sale/view/{id}', [OrderController::class, 'orderSaleView'])->name('order.sale.view');
});
