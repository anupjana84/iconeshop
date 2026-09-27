<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EnquaryController;
use App\Http\Controllers\HelplineController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceScanController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/sales-export', [SaleController::class, 'export'])->name('sales.export');
Route::get('/customers-export', [CustomerController::class, 'export'])->name('customers.export');

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    return 'All caches (config, route, view, and app) cleared successfully!';
});

Route::get('/run-queue-once', function () {
    Artisan::call('queue:work --once --tries=3');

    return response()->json(['status' => 'Processed pending jobs once']);
});

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/support', [HomeController::class, 'support'])->name('support');
Route::post('/enquiry-submit', [EnquaryController::class, 'store'])->name('enquiry.submit');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login_page');
    Route::get('/admin', [LoginController::class, 'adminLogin'])->name('admin_login');
    Route::get('/manager', [LoginController::class, 'managerLogin'])->name('manager_login');
    Route::get('/salesman', [LoginController::class, 'salesmanLogin'])->name('salesman_login');

    Route::get('/login2', [LoginController::class, 'showLoginForm2'])->name('login_page2');
    Route::get('/seller', [LoginController::class, 'seller'])->name('seller2');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/register', [LoginController::class, 'create'])->name('register');
    Route::post('/register', [LoginController::class, 'store']);
});

Route::get('/checkout/login', function () {
    session(['url.intended' => route('home', ['checkout' => 1])]);

    return redirect()->route('login_page');
})->name('checkout.login');

Route::post('guest/order', [HomeController::class, 'guestOrder'])->name('guest.order');
Route::post('/guest/check-phone', [HomeController::class, 'checkPhone'])->name('guest.checkPhone');
Route::post('/guest/send-otp', [HomeController::class, 'sendOtp'])->name('guest.sendOtp');
Route::post('/guest/verify-otp', [HomeController::class, 'verifyOtp'])->name('guest.verifyOtp');

require __DIR__ . '/web/admin.php';

Route::resource('tollfree', HelplineController::class);
Route::get('/admin/service/settings', function () {
    return view('admin.service.serve.index', ['page_title' => 'Service Settings']);
});

Route::post('/admin/service/scan-invoice', [InvoiceScanController::class, 'scanInvoice'])->name('service.scan.invoice');
Route::post('/admin/service/scan-ocr', function (\Illuminate\Http\Request $request) {
    return response()->json(['success' => false, 'message' => 'Client side OCR enabled']);
})->name('scan.ocr');

Route::get('/admin/customer/by-phone', [ServiceController::class, 'byPhone'])->name('customer.by.phone');
Route::middleware(['auth', 'role:user'])
    ->get('/user/service', [ServiceController::class, 'userservice'])
    ->name('user.service');
Route::get('/get-customer-by-phone/{phone}', [CustomerController::class, 'getByPhone'])->name('customer.getByPhone');

Route::post('/admin/service-booking', [ServiceController::class, 'savedata'])->name('service-booking.store');


Route::get('/admin/service-bookings', [ServiceController::class, 'bookings'])->name('service-booking.bookings');
Route::get('/admin/service-settings', [ServiceController::class, 'index1'])->name('service-booking.index');
Route::post('/service-booking/{id}/case-id', [ServiceController::class, 'updateCaseId'])->name('service-booking.case-id.update');
Route::post('/service-booking/{id}/status', [ServiceController::class, 'updateStatus'])->name('service-booking.status.update');
Route::delete('/admin/service-booking/{id}', [ServiceController::class, 'destroy'])->name('service-booking.destroy');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/userdashboard', [HomeController::class, 'userdashboard'])->name('userdashboard');
});

Route::middleware(['auth', 'role:salesman'])->group(function () {
    Route::get('/salesmandashboard', [HomeController::class, 'salesmandashboard'])->name('salesmandashboard');
});

Route::middleware(['auth', 'role:seller,subdealer'])->group(function () {
    Route::get('/sellerdashboard', [HomeController::class, 'sellerDashboard'])->name('sellerdashboard');
});

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
});
