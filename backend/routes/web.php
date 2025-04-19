<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\PaymentController;

Route::get('/', fn() => view('welcome'));
Route::get('/login', fn() => redirect(config('app.frontend_url').'/signin'))->name('login');

// 1) Top‑up form
Route::get('/balance/add/{user}', [PaymentController::class,'showAddBalanceForm'])
     ->name('balance.add');

// 2) Form POST
Route::post('/balance/process', [PaymentController::class,'process'])
     ->name('balance.process');

// 3) Named callbacks (must match post_data)
Route::match(['get','post'], '/payment/ssl-success', [PaymentController::class,'sslSuccess'])
     ->name('payment.ssl-success');
Route::match(['get','post'], '/payment/ssl-fail',    [PaymentController::class,'sslFail'])
     ->name('payment.ssl-fail');
Route::match(['get','post'], '/payment/ssl-cancel',  [PaymentController::class,'sslCancel'])
     ->name('payment.ssl-cancel');
Route::post(                '/payment/ssl-ipn',     [PaymentController::class,'sslIpn'])
     ->name('payment.ssl-ipn');

// 4) Catch sandbox defaults
Route::match(['get','post'], '/success', [PaymentController::class,'sslSuccess']);
Route::match(['get','post'], '/fail',    [PaymentController::class,'sslFail']);
Route::match(['get','post'], '/cancel',  [PaymentController::class,'sslCancel']);
