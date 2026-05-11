<?php

use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/products', 'pages::all-products')->name('products');
Route::livewire('/product/{slug}', 'pages::product')->name('product');
Route::livewire('/order-request', 'pages::order-request')->name('order-request');
Route::livewire('/orders', 'pages::orders')->name('orders');


Route::get('/oauth/{provider}/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/oauth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');