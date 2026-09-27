<?php

use App\Http\Controllers\ExternalServiceController;
use App\Http\Controllers\InhouseServiceController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('service/call/logs', [ServiceController::class, 'serviceCallLogs'])->name('service.call.logs');
Route::get('service/call/pending', [ServiceController::class, 'pendingServiceCalls'])->name('service.call.pending');
Route::get('service/call/completed', [ServiceController::class, 'completedServiceCalls'])->name('service.call.completed');
Route::get('service/call/canceled', [ServiceController::class, 'canceledServiceCalls'])->name('service.call.canceled');
Route::get('/service-status/create/{serviceCall}', [ServiceController::class, 'create'])->name('service-status.create');
Route::post('/service-status/store', [ServiceController::class, 'store'])->name('service-status.store');
Route::get('/service-status/edit/{id}', [ServiceController::class, 'edit'])->name('service-status.edit');
Route::post('/service-status/update/{id}', [ServiceController::class, 'update'])->name('service-status.update');

// Inhouse Service Routes
Route::post('/service/inhouse/store', [InhouseServiceController::class, 'store'])->name('inhouse.service.store');
Route::get('/service/inhouse/list', [InhouseServiceController::class, 'index'])->name('inhouse.service.list');
Route::get('/service/inhouse/counts', [InhouseServiceController::class, 'counts'])->name('inhouse.service.counts');
Route::post('/service/inhouse/{id}/update', [InhouseServiceController::class, 'update'])->name('inhouse.service.update');
Route::post('/service/inhouse/{id}/status', [InhouseServiceController::class, 'updateStatus'])->name('inhouse.service.status');
Route::post('/service/inhouse/{id}/field', [InhouseServiceController::class, 'updateField'])->name('inhouse.service.field');
Route::delete('/service/inhouse/{id}', [InhouseServiceController::class, 'destroy'])->name('inhouse.service.destroy');

// External Service Routes
Route::post('/service/external/store', [ExternalServiceController::class, 'store'])->name('external.service.store');
Route::get('/service/external/list', [ExternalServiceController::class, 'index'])->name('external.service.list');
Route::get('/service/external/counts', [ExternalServiceController::class, 'counts'])->name('external.service.counts');
Route::post('/service/external/{id}/update', [ExternalServiceController::class, 'update'])->name('external.service.update');
Route::post('/service/external/{id}/status', [ExternalServiceController::class, 'updateStatus'])->name('external.service.status');
Route::post('/service/external/{id}/field', [ExternalServiceController::class, 'updateField'])->name('external.service.field');
Route::delete('/service/external/{id}', [ExternalServiceController::class, 'destroy'])->name('external.service.destroy');

// Company Service Call Routes
Route::delete('/admin/service-booking/{id}', [ServiceController::class, 'destroy'])->name('service-booking.destroy');
