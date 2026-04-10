<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/products', 'pages::all-products')->name('products');
Route::livewire('/product', 'pages::product')->name('product');
