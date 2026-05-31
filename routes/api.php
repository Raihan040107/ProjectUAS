<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JawabanController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\UsahaController;
use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/faqs', [FaqController::class, 'index']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/admin/users', [AuthController::class, 'adminUsers']);
    Route::get('/admin/usaha', [UsahaController::class, 'adminIndex']);
    Route::put('/admin/usaha/{id}/verifikasi', [UsahaController::class, 'verify']);
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
    Route::delete('/admin/usaha/{id}', [UsahaController::class, 'destroy']);

    Route::get('/admin/dokumen', function (\Illuminate\Http\Request $request) {
        $path = $request->query('path');
        $fullPath = storage_path('app/public/' . $path);

        if (!$path || !file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    });

    // FAQ — Admin CRUD (cukup ikuti pola yang sudah ada)
    Route::get('/admin/faqs',              [FaqController::class, 'adminIndex']);
    Route::post('/admin/faqs',             [FaqController::class, 'store']);
    Route::post('/admin/faqs/reorder',     [FaqController::class, 'reorder']); // ← HARUS sebelum {faq}
    Route::put('/admin/faqs/{faq}',        [FaqController::class, 'update']);
    Route::delete('/admin/faqs/{faq}',     [FaqController::class, 'destroy']);
});
