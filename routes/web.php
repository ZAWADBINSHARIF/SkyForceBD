<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/products', 'pages::all-products')->name('products');
Route::livewire('/product', 'pages::product')->name('product');
Route::livewire('/order-request', 'pages::order-request')->name('order-request');
Route::livewire('/orders', 'pages::orders')->name('orders');
