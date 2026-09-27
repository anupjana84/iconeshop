<?php

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use Illuminate\Support\Facades\Route;

Route::prefix('purchase')->group(function () {
    Route::get('create', [PurchaseController::class, 'purchaseCreate'])->name('purchase.create');
    Route::post('store', [PurchaseController::class, 'purchaseStore'])->name('purchase.store');
    Route::get('/', [PurchaseController::class, 'purchaseList'])->name('purchase.list');
    Route::get('show/{id}', [PurchaseController::class, 'purchaseShow'])->name('purchase.show');
    Route::get('barcode/{id}', [PurchaseController::class, 'purchaseBarcode'])->name('purchase.barcode');
});

Route::prefix('purchase/return')->group(function () {
    Route::post('/', [PurchaseReturnController::class, 'purchaseReturn'])->name('purchase.return');
    Route::get('list', [PurchaseReturnController::class, 'returnList'])->name('purchase.return.list');
    Route::get('show/{id}', [PurchaseReturnController::class, 'returnShow'])->name('purchase.return.show');
});

Route::prefix('sale')->group(function () {
    Route::get('create', [SaleController::class, 'saleCreate'])->name('sale.create');
    Route::post('store', [SaleController::class, 'saleStore'])->name('sale.store');
    Route::get('/', [SaleController::class, 'saleList'])->name('sale.list');
    Route::get('show/{id}', [SaleController::class, 'saleShow'])->name('sale.show');
    Route::get('invoice/{id}', [SaleController::class, 'saleInvoice'])->name('sale.invoice');
    Route::post('update', [SaleController::class, 'saleUpdate'])->name('sale.update');
});

Route::prefix('sale/return')->group(function () {
    Route::post('/', [SaleReturnController::class, 'saleReturn'])->name('sale.return');
    Route::get('list', [SaleReturnController::class, 'returnList'])->name('sale.return.list');
    Route::get('show/{id}', [SaleReturnController::class, 'returnShow'])->name('sale.return.show');
});
