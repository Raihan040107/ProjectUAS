<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    private function isAdmin(Request $request): bool
    {
        return $request->user() && $request->user()->id_role === 2;
    }

    public function index()
    {
        return response()->json([
            'data' => Pertanyaan::all(),
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Hanya admin yang boleh menambah pertanyaan.',
            ], 403);
        }

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
        ]);

        $pertanyaan = Pertanyaan::create($validated);

        return response()->json([
            'message' => 'Pertanyaan berhasil ditambahkan',
            'data'    => $pertanyaan,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Hanya admin yang boleh mengubah pertanyaan.',
            ], 403);
        }

        $pertanyaan = Pertanyaan::where('pertanyaan_id', $id)->firstOrFail();

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
        ]);

        $pertanyaan->update($validated);

        return response()->json([
            'message' => 'Pertanyaan berhasil diperbarui.',
            'data'    => $pertanyaan,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'message' => 'Hanya admin yang boleh menghapus pertanyaan.',
            ], 403);
        }

        $pertanyaan = Pertanyaan::where('pertanyaan_id', $id)->firstOrFail();
        $pertanyaan->delete();

        return response()->json([
            'message' => 'Pertanyaan berhasil dihapus.',
        ]);
    }
}
