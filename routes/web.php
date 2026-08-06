<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/products');
});

Route::get('/products', function () {
    return view('products');
});
