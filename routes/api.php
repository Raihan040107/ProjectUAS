<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\UsahaController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/pertanyaan', [PertanyaanController::class, 'index']);
    Route::get('/usaha', [UsahaController::class, 'index']);
    Route::post('/usaha', [UsahaController::class, 'store']);
    Route::post('/jawaban', [JawabanController::class, 'store']);
    Route::get('/jawaban/analisis', [JawabanController::class, 'analysis']);
    Route::post('/admin/pertanyaan', [PertanyaanController::class, 'store']);
});
