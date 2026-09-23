<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// prikaz Register Stranice
Route::get('/register', [RegisterController::class, 'index'])->name('register')->middleware(['guest', 'signed', 'nocache']);

// Register - rate limiting
Route::post('/register', [RegisterController::class, 'store'])->name('register.store')->middleware(['guest', 'throttle:6,1']);


// prikaz Login Stranice
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware(['guest', 'nocache']);
// Login - rate limiting
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware(['guest', 'throttle:6,1']);
//Logout
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

//Dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('superadmin')) {
        $stores = App\Models\Store::whereHas('user')->get();
    } else {
        $stores = auth()->user()->stores;
    }
    return view('admin.dashboard', ['stores' => $stores]);
})->middleware(['auth', 'nocache'])->name('dashboard');

//Store Create
Route::get('/store', [StoreController::class, 'create'])->name('store.create')->middleware('auth');
Route::post('/store', [StoreController::class, 'store'])->name('store.store')->middleware('auth');

//Store prikaz stranice
Route::get('/store/{store}', [StoreController::class, 'show'])->name('store.show')->middleware('auth');
//Store update
Route::put('/store/{store}', [StoreController::class, 'update'])->name('store.update')->middleware('auth');


//pravljenje Bundle-a
Route::post('store/{store}/bundle', [BundleController::class, 'store'])->name('bundle.store')->middleware('auth');
// destroy bundle
Route::delete('/bundle/{bundle}', [BundleController::class, 'destroy'])->name('bundle.destroy')->middleware('auth');
//prikaz kupona za bundle
Route::get('/bundle/{bundle}', [BundleController::class, 'show'])->name('bundle.show')->middleware('auth');


//kuponi
Route::get('/coupon/{coupon}/edit', [CouponController::class, 'edit'])->name('coupon.edit')->middleware('auth');
Route::put('/coupon/{coupon}', [CouponController::class, 'update'])->name('coupon.update')->middleware('auth');

// brisanje kup
Route::delete('/coupon/{coupon}', [CouponController::class, 'destroy'])->name('coupon.destroy')->middleware('auth');

// Toggle iskoriscen
Route::post('/coupon/{coupon}/toggle-used', [CouponController::class, 'toggleUsed'])->name('coupon.toggle-used')->middleware('auth');

//store new kupon
Route::post('/bundle/{bundle}/coupon', [CouponController::class, 'store'])->name('coupon.store')->middleware('auth');

//unsubscribe view
Route::get('/coupon/{coupon}/unsubscribe', [CouponController::class, 'unsubscribe'])->name('coupons.unsubscribe')->middleware('guest');


// Admin invite
Route::get('/superadmin/invite', [InviteController::class, 'create'])->name('invite.create')->middleware(['auth', 'superadmin']);
Route::post('/superadmin/invite', [InviteController::class, 'store'])->name('invite.store')->middleware(['auth', 'superadmin', 'throttle:6,1']);

// Admin Upravljanje adminima
Route::get('/superadmin/admins', [AdminController::class, 'index'])->name('admins.index')->middleware(['auth', 'superadmin']);
//Admin brise admin
Route::delete('superadmin/admins/{user}', [AdminController::class, 'destroy'])->name('admins.destroy')->middleware(['auth', 'superadmin']);
Route::post('superadmin/admins/{user}', [AdminController::class, 'edit'])->name('admins.update')->middleware(['auth', 'superadmin']);


//-----------------EXPORT---------------
Route::post('/store/{store}/export-codes', [StoreController::class, 'exportCodes'])->name('store.export-codes')->middleware('auth');
// SUPERADMIN EXPORT ALL
Route::post('/superadmin/export-all', [AdminController::class, 'exportAll'])->name('superadmin.export-all')->middleware(['auth', 'superadmin']);
// EDIT ADMIN-a SUPERADMIN
Route::put('superadmin/admins/{admin}', [AdminController::class, 'update'])->name('admins.update')->middleware(['auth', 'superadmin']);


//----------------IMPORT------------------
Route::post('bundle/{bundle}/import-codes', [CouponController::class, 'importCodes'])->name('coupon.import')->middleware('auth');

// -----------RESEND ALL ----------------------
Route::post('coupon/{coupon}/resend-initial', [CouponController::class, 'resendInitial'])->name('coupon.resend-initial')->middleware('auth');
Route::post('coupon/{coupon}/resend-reminder', [CouponController::class, 'resendReminder'])->name('coupon.resend-reminder')->middleware('auth');
Route::post('/bundle/{bundle}/resend-all', [BundleController::class, 'resendAll'])->name('bundle.resend-all')->middleware('auth');


//------------EMAIL UPDATE TEMPLATE---------
Route::put('/store/{store}/email-templates', [StoreController::class, 'updateEmailTemplates'])->name('store.update-email-templates')->middleware('auth');
