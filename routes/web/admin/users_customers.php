<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserControllers;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::get('list', [UserControllers::class, 'usersList'])->name('users.list');
    Route::get('show/{id}', [UserControllers::class, 'userShow'])->name('user.show');
    Route::get('create', [UserControllers::class, 'userCreate'])->name('user.create');
    Route::post('store', [UserControllers::class, 'userstore'])->name('user.store');
    Route::delete('delete/{id}', [UserControllers::class, 'userDelete'])->name('user.delete');
    Route::get('edit/{id}', [UserControllers::class, 'userEdit'])->name('user.edit');
    Route::put('update/{id}', [UserControllers::class, 'userUpdate'])->name('user.update');
    Route::get('managers', [UserControllers::class, 'managersList'])->name('managers.list');
    Route::get('salesmans', [UserControllers::class, 'salesmansList'])->name('salesmans.list');
    Route::get('subdealers', [UserControllers::class, 'subDealersList'])->name('subdealers.list');
    Route::get('change/status/{id}', [UserControllers::class, 'changeStatus'])->name('user.change.status');
    Route::post('change/password/{id}', [UserControllers::class, 'changePassword'])->name('user.change.password');
});

Route::prefix('customer')->group(function () {
    Route::get('list', [CustomerController::class, 'customersList'])->name('customers.list');
    Route::get('create', [CustomerController::class, 'customersCreate'])->name('customers.create');
    Route::post('store', [CustomerController::class, 'customersStore'])->name('customers.store');
    Route::get('edit/{id}', [CustomerController::class, 'customersEdit'])->name('customers.edit');
    Route::put('update/{id}', [CustomerController::class, 'customersUpdate'])->name('customers.update');
    Route::delete('delete/{id}', [CustomerController::class, 'customersRemove'])->name('customers.remove');
});
