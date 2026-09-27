<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\EmiController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RewardPointController;
use Illuminate\Support\Facades\Route;

Route::prefix('ledger')->group(function () {
    Route::get('/', [LedgerController::class, 'ledger'])->name('ledger.list');
    Route::get('customers', [LedgerController::class, 'customerLedger'])->name('ledger.customer');
    Route::get('company', [LedgerController::class, 'companyLedger'])->name('ledger.company');
    Route::get('subDealer', [LedgerController::class, 'subDealerLedger'])->name('ledger.subDealer');
    Route::get('salesman', [LedgerController::class, 'salesmanLedger'])->name('ledger.salesman');
    Route::get('show/{id}', [LedgerController::class, 'ledgerShow'])->name('ledger.show');
    Route::get('entry', [LedgerController::class, 'previousEntry'])->name('ledger.previous.entry');
    Route::post('entry/save', [LedgerController::class, 'previousEntrySave'])->name('ledger.previous.entry.save');
});

Route::prefix('payment')->group(function () {
    Route::get('history', [PaymentController::class, 'paymentHistory'])->name('payment.history');
    Route::get('create', [PaymentController::class, 'paymentCreate'])->name('payment.create');
    Route::post('store', [PaymentController::class, 'paymentStore'])->name('payment.store');
});

Route::get('reward-point/history', [RewardPointController::class, 'pointHistory'])->name('reward.point.history');
Route::get('reward-point/details/{mobile}', [RewardPointController::class, 'details'])->name('reward.point.details');
Route::get('/reward-point/balance/{mobile}', [RewardPointController::class, 'getBalance'])->name('reward.point');

Route::prefix('expenses')->group(function () {
    Route::get('payment/list', [ExpensesController::class, 'expensesPaymentList'])->name('expenses.payment.list');
    Route::get('create', [ExpensesController::class, 'expensesCreate'])->name('expenses.create');
    Route::post('store', [ExpensesController::class, 'expensesStore'])->name('expenses.store');
    Route::get('/', [ExpensesController::class, 'expensesList'])->name('expenses.list');
    Route::get('edit/{id}', [ExpensesController::class, 'expensesEdit'])->name('expenses.edit');
    Route::post('update/{id}', [ExpensesController::class, 'expensesUpdate'])->name('expenses.update');
});

Route::prefix('bank')->group(function () {
    Route::get('create', [BankController::class, 'bankCreate'])->name('bank.create');
    Route::post('store', [BankController::class, 'bankStore'])->name('bank.store');
    Route::get('/', [BankController::class, 'bankList'])->name('bank.list');
    Route::get('edit/{id}', [BankController::class, 'bankEdit'])->name('bank.edit');
    Route::post('update/{id}', [BankController::class, 'bankUpdate'])->name('bank.update');
    Route::get('statement/{id}', [BankController::class, 'bankStatement'])->name('bank.statement');
    Route::get('add_balance/{id}', [BankController::class, 'addBalancePage'])->name('bank.addBalance.page');
    Route::post('add/balance/{id}', [BankController::class, 'addBalance'])->name('bank.addBalance');
});

Route::prefix('emi')->group(function () {
    Route::get('emi', [EmiController::class, 'index'])->name('emi.list');
    Route::get('pay/{id}', [EmiController::class, 'pay'])->name('emi.pay');
    Route::post('clear', [EmiController::class, 'emiClear'])->name('emi.clear');
});

Route::prefix('finance')->group(function () {
    Route::get('create', [EmiController::class, 'create'])->name('finance.create');
    Route::post('store', [EmiController::class, 'financeStore'])->name('finance.store');
    Route::get('edit/{id}', [EmiController::class, 'financeEdit'])->name('finance.edit');
    Route::post('update/{id}', [EmiController::class, 'financeUpdate'])->name('finance.update');
    Route::get('list', [EmiController::class, 'financeList'])->name('finance.list');
});
