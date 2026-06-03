<?php

namespace App\Http\Controllers;

use App\Models\Keunggulan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeunggulanController extends Controller
{
    // PUBLIC — GET /api/keunggulan
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => Keunggulan::active()->get(['id', 'nomor', 'judul', 'deskripsi', 'order']),
        ]);
    }

    // ADMIN — GET /api/admin/keunggulan
    public function adminIndex(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => Keunggulan::orderBy('order')->get(),
        ]);
    }

    // POST /api/admin/keunggulan
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nomor'     => 'required|string|max:10',
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'order'     => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $item = Keunggulan::create([
            'nomor'     => $request->nomor,
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'order'     => $request->input('order', Keunggulan::max('order') + 1),
            'is_active' => true,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Keunggulan berhasil ditambahkan.', 'data' => $item], 201);
    }

    // PUT /api/admin/keunggulan/{keunggulan}
    public function update(Request $request, Keunggulan $keunggulan): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nomor'     => 'sometimes|required|string|max:10',
            'judul'     => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'order'     => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $keunggulan->update($request->only(['nomor', 'judul', 'deskripsi', 'order', 'is_active']));

        return response()->json(['status' => 'success', 'message' => 'Keunggulan berhasil diperbarui.', 'data' => $keunggulan->fresh()]);
    }

    // DELETE /api/admin/keunggulan/{keunggulan}
    public function destroy(Keunggulan $keunggulan): JsonResponse
    {
        $keunggulan->delete();
        return response()->json(['status' => 'success', 'message' => 'Keunggulan berhasil dihapus.']);
    }

    // POST /api/admin/keunggulan/reorder
    public function reorder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:keunggulan,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        foreach ($request->ids as $order => $id) {
            Keunggulan::where('id', $id)->update(['order' => $order + 1]);
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan keunggulan berhasil diperbarui.']);
    }
}
