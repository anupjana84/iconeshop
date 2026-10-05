<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('product')->group(function () {
    Route::get('create', [ProductController::class, 'productCreate'])->name('product.create');
    Route::get('list', [ProductController::class, 'productList'])->name('product.list');
    Route::get('special-offers', [ProductController::class, 'specialOffersList'])->name('product.special.offers');
    Route::get('empty/stock', [ProductController::class, 'emptyStockProducts'])->name('product.empty.stock');
    Route::get('old/stock', [ProductController::class, 'oldStockProducts'])->name('product.old.stock');
    Route::get('code', [ProductController::class, 'productCode'])->name('product.code');
    Route::post('store', [ProductController::class, 'store'])->name('products.store');
    Route::get('show/{id}', [ProductController::class, 'productShow'])->name('product.show');
    Route::get('edit/{id}', [ProductController::class, 'productEdit'])->name('product.edit');
    Route::put('update/{id}', [ProductController::class, 'productUpdate'])->name('product.update');
    Route::post('/bulk-price-update', [ProductController::class, 'bulkPriceUpdate'])->name('bulk.price.update');
    Route::post('image/add/{id}', [ProductController::class, 'productImageAdd'])->name('product.image.add');
    Route::post('thambnail/{id}', [ProductController::class, 'productThambnail'])->name('product.thambnail');
    Route::get('change/status/{id}', [ProductController::class, 'productChangeStatus'])->name('product.change.status');
    Route::delete('delete/{id}', [ProductController::class, 'productDestroy'])->name('product.delete');
    Route::put('{id}/update-display', [ProductController::class, 'updateDisplay'])->name('product.updateDisplay');
    Route::post('/update-special-offer', [ProductController::class, 'updateSpecialOffer'])->name('product.update.special.offer');
    Route::get('/{productId}/special-offer', [ProductController::class, 'getSpecialOffer'])->name('product.special.offer.get');
    Route::post('/special-offer/save', [ProductController::class, 'saveSpecialOffer'])->name('product.special.offer.save');
    Route::delete('/special-offer/{id}', [ProductController::class, 'deleteSpecialOffer'])->name('product.special.offer.delete');
    
    // Wildcard route MUST be last in this group
    Route::get('{code}', [ProductController::class, 'productByCode'])->name('product.by.code');
});

Route::get('/get-dependent-data', [ProductController::class, 'getDependentData'])->name('get.dependent.data');
Route::get('/product/barcode/{code}', [ProductController::class, 'productBarcode'])->name('product.barcode');

Route::prefix('category')->group(function () {
    Route::get('/gst/{id}', [CategoryController::class, 'getGst'])->name('category.gst');
    Route::get('/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::get('/', [CategoryController::class, 'categoryList'])->name('category.list');
});

Route::prefix('brand')->group(function () {
    Route::get('/create', [BrandController::class, 'brandCreate'])->name('brand.create');
    Route::get('/', [BrandController::class, 'brandList'])->name('brand.list');
    Route::post('/store', [BrandController::class, 'brandStore'])->name('brand.store');
    Route::get('/edit/{id}', [BrandController::class, 'brandEdit'])->name('brand.edit');
    Route::post('/update/{id}', [BrandController::class, 'brandUpdate'])->name('brand.update');
});

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('companies.list');
    Route::get('/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/store', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::post('/{id}/update', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');
});
