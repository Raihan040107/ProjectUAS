<?php

namespace App\Http\Controllers;

use App\Models\StudiKasus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudiKasusController extends Controller
{
    /* ─────────────────────────────────────────────
     * PUBLIC — GET /api/studi-kasus
     * ───────────────────────────────────────────── */
    public function index(): JsonResponse
    {
        $data = StudiKasus::active()
            ->get(['id', 'nomor', 'nama_usaha', 'deskripsi', 'pencapaian', 'order']);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /* ─────────────────────────────────────────────
     * ADMIN — GET /api/admin/studi-kasus
     * ───────────────────────────────────────────── */
    public function adminIndex(): JsonResponse
    {
        $data = StudiKasus::orderBy('order')->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /* ─────────────────────────────────────────────
     * POST /api/admin/studi-kasus
     * ───────────────────────────────────────────── */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nomor'        => 'required|string|max:10',
            'nama_usaha'   => 'required|string|max:255',
            'deskripsi'    => 'required|string',
            'pencapaian'   => 'required|array|min:1',
            'pencapaian.*' => 'required|string|max:255',
            'order'        => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $order = $request->input('order', StudiKasus::max('order') + 1);

        $item = StudiKasus::create([
            'nomor'      => $request->nomor,
            'nama_usaha' => $request->nama_usaha,
            'deskripsi'  => $request->deskripsi,
            'pencapaian' => $request->pencapaian,
            'order'      => $order,
            'is_active'  => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Studi kasus berhasil ditambahkan.',
            'data'    => $item,
        ], 201);
    }

    /* ─────────────────────────────────────────────
     * PUT /api/admin/studi-kasus/{studiKasus}
     * ───────────────────────────────────────────── */
    public function update(Request $request, StudiKasus $studiKasus): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nomor'        => 'sometimes|required|string|max:10',
            'nama_usaha'   => 'sometimes|required|string|max:255',
            'deskripsi'    => 'sometimes|required|string',
            'pencapaian'   => 'sometimes|required|array|min:1',
            'pencapaian.*' => 'required|string|max:255',
            'order'        => 'sometimes|integer|min:0',
            'is_active'    => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $studiKasus->update(
            $request->only(['nomor', 'nama_usaha', 'deskripsi', 'pencapaian', 'order', 'is_active'])
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Studi kasus berhasil diperbarui.',
            'data'    => $studiKasus->fresh(),
        ]);
    }

    /* ─────────────────────────────────────────────
     * DELETE /api/admin/studi-kasus/{studiKasus}
     * ───────────────────────────────────────────── */
    public function destroy(StudiKasus $studiKasus): JsonResponse
    {
        $studiKasus->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Studi kasus berhasil dihapus.',
        ]);
    }

    /* ─────────────────────────────────────────────
     * POST /api/admin/studi-kasus/reorder
     * Body: { "ids": [2, 1, 3] }
     * ───────────────────────────────────────────── */
    public function reorder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:studi_kasus,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        foreach ($request->ids as $order => $id) {
            StudiKasus::where('id', $id)->update(['order' => $order + 1]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Urutan studi kasus berhasil diperbarui.',
        ]);
    }
}
