<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BundleController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\InviteController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

// login
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
// register
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
// unsubscribe link
Route::get('coupons/{coupon}/unsubscribe', [CouponController::class, 'unsubscribe'])->middleware('signed')->name('api.coupons.unsubscribe');
// auth routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/stores', [StoreController::class, 'index']); // collection of stores
    Route::get('/stores/{store}', [StoreController::class, 'show']); // show one store
    Route::post('/stores', [StoreController::class, 'store']);  // store one store
    Route::put('/stores/{store}', [StoreController::class, 'update']); // update store
    Route::put('/stores/{store}/email-templates', [StoreController::class, 'updateEmailTemplate']);

    Route::get('stores/{store}/bundles', [BundleController::class, 'index']); // list of bundles
    Route::post('stores/{store}/bundles', [BundleController::class, 'store']); // store one bundle
    Route::get('/bundles/{bundle}', [BundleController::class, 'show']);  // show one bundle
    Route::delete('/bundles/{bundle}', [BundleController::class, 'destroy']); // delete one bundle

    Route::get('/bundles/{bundle}/coupons', [CouponController::class, 'index']); // collection of coupons
    Route::get('/coupons/{coupon}', [CouponController::class, 'show']); // get one coupon
    Route::post('/bundles/{bundle}/coupons', [CouponController::class, 'store']); // post coupon
    Route::put('/coupons/{coupon}', [CouponController::class, 'update']); // update coupon
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy']); // delete coupon
    Route::post('/coupons/{coupon}/resend-initial', [CouponController::class, 'resendInitial']); // posalji initial
    Route::post('/coupons/{coupon}/resend-reminder', [CouponController::class, 'resendReminder']); // posalji reminder
    Route::post('/bundles/{bundle}/resend-all', [BundleController::class, 'resendAll']); // posalji SVE

    //EXPORT/IMPORT USER
    Route::post('/bundles/{bundle}/import-codes', [CouponController::class, 'importCodes']);
    Route::post('/stores/{store}/export-codes', [StoreController::class, 'exportCodes']);

    // toogle used coupon
    Route::post('/coupons/{coupon}/toogle-used', [CouponController::class, 'toogleUsed']);
});

// SUPERADMIN roles

Route::middleware(['auth:sanctum', 'superadmin'])->group(function () {
    Route::get('/admins', [AdminController::class, 'index']);
    Route::put('/admins/{admin}', [AdminController::class, 'update']);
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy']);

    // invete route
    Route::post('/invite', [InviteController::class, 'store']);

    //Export All Stores SuperAdmin
    Route::post('/export-all', [AdminController::class, 'exportAllCodes']);
});
