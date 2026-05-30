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
    Route::get('/admin/users', [AuthController::class, 'adminUsers']);
    Route::get('/admin/usaha', [UsahaController::class, 'adminIndex']);
    Route::put('/admin/usaha/{id}/verifikasi', [UsahaController::class, 'verify']);
    // Route::put('/admin/pertanyaan/{id}', [PertanyaanController::class, 'update']);
    // Route::delete('/admin/pertanyaan/{id}', [PertanyaanController::class, 'destroy']);

    // Route::get('/pertanyaan', [PertanyaanController::class, 'index']);
    // Route::get('/usaha', [UsahaController::class, 'index']);
    // Route::post('/usaha', [UsahaController::class, 'store']);
    // Route::post('/jawaban', [JawabanController::class, 'store']);
    // Route::get('/jawaban/analisis', [JawabanController::class, 'analysis']);
    // Route::post('/admin/pertanyaan', [PertanyaanController::class, 'store']);
    Route::get('/usaha', [UsahaController::class, 'index']);
    Route::post('/usaha', [UsahaController::class, 'store']);

    // Jawaban
    Route::post('/jawaban', [JawabanController::class, 'store']);
    Route::get('/jawaban/analisis', [JawabanController::class, 'analysis']);

    // Pertanyaan
    Route::get('/pertanyaan', [PertanyaanController::class, 'index']);
    Route::get('/pertanyaan/{id}', [PertanyaanController::class, 'show']);

    Route::post('/pertanyaan', [PertanyaanController::class, 'store']);
    Route::put('/pertanyaan/{id}', [PertanyaanController::class, 'update']);
    Route::delete('/pertanyaan/{id}', [PertanyaanController::class, 'destroy']);

    // Opsi Jawaban
    Route::post('/pertanyaan/{id}/opsi', [PertanyaanController::class, 'storeOpsi']);

    // sync HARUS sebelum {opsiId}
    Route::put('/pertanyaan/{id}/opsi/sync', [PertanyaanController::class, 'syncOpsi']);

    Route::put('/pertanyaan/{id}/opsi/{opsiId}', [PertanyaanController::class, 'updateOpsi']);
    Route::delete('/pertanyaan/{id}/opsi/{opsiId}', [PertanyaanController::class, 'destroyOpsi']);
});
