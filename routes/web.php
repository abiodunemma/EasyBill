<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaystackController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::post('/paystack/initialize', [PaystackController::class, 'initializePayment'])->name('paystack.initialize');
Route::get('/paystack/callback', [PaystackController::class, 'handlePaymentCallback']);
