<?php

use App\Http\Controllers\GstControllers;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Route;

Route::prefix('report')->group(function () {
    Route::get('customer/due', [LedgerController::class, 'dueCustomerReport'])->name('report.customer.due');
    Route::get('company/due', [LedgerController::class, 'dueCompanyReport'])->name('report.company.due');
    Route::get('/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
    Route::get('/sales/date/{date}', [ReportController::class, 'getByDate'])->name('sales.byDate');
    Route::get('gst', [GstControllers::class, 'gstReports'])->name('gst.reports');
    Route::get('add/remark/{id}', [LedgerController::class, 'addRemarkPage'])->name('report.add.remark');
    Route::post('update/remark/{id}', [LedgerController::class, 'addRemark'])->name('add.remark');
});

Route::get('/invoice/{id}/send-wp', [WhatsappController::class, 'sendWhatsAppInvoice'])->name('invoice.send.wp');
Route::get('/reward-point/send-wp/{mobile}', [WhatsappController::class, 'sendRewardPointWhatsapp'])->name('reward.point.send.wp');
Route::get('/bulk-whatsapp', [WhatsappController::class, 'index'])->name('bulk.whatsapp');
Route::post('/bulk-whatsapp/send', [WhatsappController::class, 'send'])->name('bulk.whatsapp.send');
Route::get('/service/{id}/send-wp', [WhatsappController::class, 'sendWhatsappService'])->name('service.send.wp');
Route::post('/report/gst/generate', [GstControllers::class, 'generateReport'])->name('gst.report.generate');
Route::get('/report/gst/download/{id}', [GstControllers::class, 'downloadReport'])->name('gst.report.download');
