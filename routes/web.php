<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Semua route yang bukan /api dikembalikan ke index.html (SPA)
Route::get('/{any}', function () {
    return view('app'); // atau file index Vite kamu
})->where('any', '^(?!api).*$');
