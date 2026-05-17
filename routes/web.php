<?php

use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/products', 'pages::all-products')->name('products');
Route::livewire('/product/{slug}', 'pages::product')->name('product');
Route::livewire('/order-request', 'pages::order-request')->name('order-request');
Route::livewire('/orders', 'pages::orders')->name('orders');
Route::livewire('/page/{slug}', 'pages::additional-page');


Route::get('/oauth/{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/oauth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');


// SslCommerz Checkout pages
Route::livewire('/checkout/success', 'pages::checkout-message')->defaults('state', 'success')->name('checkout.success');
Route::livewire('/checkout/fail',    'pages::checkout-message')->defaults('state', 'fail')->name('checkout.fail');
Route::livewire('/checkout/cancel',  'pages::checkout-message')->defaults('state', 'cancel')->name('checkout.cancel');

// SSLCommerz POSTs to these — must be POST
Route::post('/checkout/success', [PaymentCallbackController::class, 'success'])->name('checkout.success.callback');
Route::post('/checkout/fail',    [PaymentCallbackController::class, 'fail'])->name('checkout.fail.callback');
Route::post('/checkout/cancel',  [PaymentCallbackController::class, 'cancel'])->name('checkout.cancel.callback');
