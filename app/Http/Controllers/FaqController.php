<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    /* ─────────────────────────────────────────────
     * PUBLIC  — tidak perlu login
     * GET /api/faqs
     * Dipakai oleh halaman Index (landing page)
     * ───────────────────────────────────────────── */
    public function index(): JsonResponse
    {
        $faqs = Faq::active()->get(['id', 'question', 'answer', 'order']);

        return response()->json([
            'status' => 'success',
            'data'   => $faqs,
        ]);
    }

    /* ─────────────────────────────────────────────
     * ADMIN — butuh login + role admin
     * GET /api/admin/faqs
     * Semua FAQ (aktif & non-aktif) untuk halaman edit
     * ───────────────────────────────────────────── */
    public function adminIndex(): JsonResponse
    {
        $faqs = Faq::orderBy('order')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $faqs,
        ]);
    }

    /* ─────────────────────────────────────────────
     * POST /api/admin/faqs
     * ───────────────────────────────────────────── */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
            'answer'   => 'required|string',
            'order'    => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Jika order tidak dikirim, taruh di akhir
        $order = $request->input('order', Faq::max('order') + 1);

        $faq = Faq::create([
            'question'  => $request->question,
            'answer'    => $request->answer,
            'order'     => $order,
            'is_active' => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'FAQ berhasil ditambahkan.',
            'data'    => $faq,
        ], 201);
    }

    /* ─────────────────────────────────────────────
     * PUT /api/admin/faqs/{faq}
     * ───────────────────────────────────────────── */
    public function update(Request $request, Faq $faq): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'question'  => 'sometimes|required|string|max:500',
            'answer'    => 'sometimes|required|string',
            'order'     => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $faq->update($request->only(['question', 'answer', 'order', 'is_active']));

        return response()->json([
            'status'  => 'success',
            'message' => 'FAQ berhasil diperbarui.',
            'data'    => $faq->fresh(),
        ]);
    }

    /* ─────────────────────────────────────────────
     * DELETE /api/admin/faqs/{faq}
     * ───────────────────────────────────────────── */
    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'FAQ berhasil dihapus.',
        ]);
    }

    /* ─────────────────────────────────────────────
     * POST /api/admin/faqs/reorder
     * Body: { "ids": [3, 1, 4, 2] }  ← array id urutan baru
     * ───────────────────────────────────────────── */
    public function reorder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:faqs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        foreach ($request->ids as $order => $id) {
            Faq::where('id', $id)->update(['order' => $order + 1]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Urutan FAQ berhasil diperbarui.',
        ]);
    }
}
