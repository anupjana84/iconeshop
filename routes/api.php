<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'loginUser']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPasswordWithOtp']);




// Route::get('/managerpayment', [HomeController::class, 'managerpayment']);
Route::get('/category', [HomeController::class, 'getAllCategory']);
// Route::get('/salespayment', [HomeController::class, 'salesmanpayment']);
Route::get('/product', [HomeController::class, 'fetchProductsWithDetails']);
Route::get('/productAll', [HomeController::class, 'fetchProductsWithDetailsAll']);
Route::get('/check-product/{id}', [HomeController::class, 'checkProductActive']);
Route::get('/product/{id}', [HomeController::class, 'fetchProductsWithDetailswithid']);
Route::get('/productbarcode/{id}', [HomeController::class, 'fetchProductsWithDetailswithBarcode']);
Route::get('/fetchTopCarousel', [HomeController::class, 'fetchTopCarousel']);
Route::get('/fetchBottomCarousel', [HomeController::class, 'fetchBottomCarousel']);
Route::get('/fetchHotCarousel', [HomeController::class, 'fetchHotCarousel']);
Route::post('/products/search', [HomeController::class, 'search']);
Route::post('/service-request', [HomeController::class, 'createService']);
Route::get('/help', [HomeController::class, 'helplinenumbers']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/payment/{id}', [PaymentController::class, 'userPayment']);
    Route::get('/point/{id}', [HomeController::class, 'pointget']);
    
    Route::post('/deleteProductImage/{id}', [HomeController::class, 'deleteProductImage']);
    Route::post('/product/details/update/{id}', [HomeController::class, 'productDetailsUpdate']);
    Route::post('/product/thambnail/{id}',[HomeController::class,'updateThambnail']);
    Route::post('product/image/add/{id}',[HomeController::class,'productImageAdd']);
    
    
    
    Route::post('/updateProductDetailsexit/{id}', [HomeController::class, 'updateProductDetailsExit']);
    
    
    
    
    
    Route::post('/update/{id}', [HomeController::class, 'uploadImage']); // for internal image uploding 
    // Route::get('/productdetails/{id}', [HomeController::class, 'fetchProductsDetails']);
    Route::get('/download/{id}', [HomeController::class, 'downloadImage']);
    Route::get('/convert', [HomeController::class, 'convertAllJpgToPng']); // for internal img convert 
    Route::get('/update-image-urls', [HomeController::class, 'updateImageUrls']);
    Route::post('/updateProductDetails', [HomeController::class, 'updateProductDetails']);
    // Route::put('/updateProductDetails/{id}', [HomeController::class, 'updateProductDetailsexit']);
    Route::middleware('auth:sanctum')->group(function () {
    });
    
    // Add your authenticated routes here
    Route::get('/user/{id}', [AuthController::class, 'getUser']);
    Route::put('/users/{id}', [HomeController::class, 'updateUser']);
    Route::get('/sales/{id}', [HomeController::class, 'getByUserId']);
    
    Route::post('/users/{id}/changepassword', [HomeController::class, 'changePasswordById']);
    
    // salesman 
    
    Route::post('/updateDetails/{id}', [HomeController::class, 'updateProductDetails2']);
    Route::post('/customer', [HomeController::class, 'customerSave']);
    Route::post('/customer-new', [HomeController::class, 'customerSave2']);
    Route::get('/customer', [HomeController::class, 'customerget']);
    
    Route::post('/order', [HomeController::class, 'orderPlace']);
    Route::get('/order/history/{id}', [HomeController::class, 'fetchOrder']);
    Route::post('/orderdetail', [HomeController::class, 'orderDetails']);
    Route::post('/updateProduct', [HomeController::class, 'updateProductDetailId']);
    //Direct Order
    Route::post('/order/direct', [HomeController::class, 'directOrderPlace']);

});


Route::get('customer/{mobile}', [HomeController::class, 'customerByMobile'])->name('customer.by.mobile');
Route::get('/get-members/{type}', [HomeController::class, 'getMembersByType'])->name('get.members.by.type');