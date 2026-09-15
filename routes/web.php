<?php

use App\Http\Controllers\BundleController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// prikaz Register Stranice
Route::get('/register',[RegisterController::class, 'index'])->name('register')->middleware('guest');
// Register
Route::post('/register', [RegisterController::class, 'store'])->name('register.store')->middleware('guest');


// prikaz Login Stranice
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
// Login
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
//Logout
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

//Dashboard
Route::get('/dashboard', function () {
    $stores = auth()->user()->stores;
    return view('admin.dashboard', ['stores'=>$stores]);
})->middleware(['auth'])->name('dashboard');


//Store Create
Route::get('/store', [StoreController::class, 'create'])->name('store.create')->middleware('auth');
Route::post('/store', [StoreController::class, 'store'])->name('store.store')->middleware('auth');


//Store prikaz stranice
Route::get('/store/{store}', [StoreController::class, 'show'])->name('store.show')->middleware('auth');


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
