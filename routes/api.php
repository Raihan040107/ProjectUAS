<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'BE connected',
        'status' => 'ok',
    ]);
});
