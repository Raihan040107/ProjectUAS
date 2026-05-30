<?php

namespace App\Http\Controllers;

use App\Models\OpsiJawaban;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PertanyaanController extends Controller
{
    // ─────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────

    private function isAdmin(Request $request): bool
    {
        return $request->user() && $request->user()->id_role === 2;
    }

    // ─────────────────────────────────────────────
    // PERTANYAAN — Read (public)
    // ─────────────────────────────────────────────

    /**
     * GET /pertanyaan
     * Kembalikan semua pertanyaan beserta opsi jawabannya,
     * diurutkan per aspek + urutan.
     * Dipakai oleh form user (dinamis) dan admin.
     */
    public function index()
    {
        $pertanyaan = Pertanyaan::with('opsiJawaban')
            ->orderByRaw("CASE aspek WHEN 'environment' THEN 1 WHEN 'social' THEN 2 WHEN 'governance' THEN 3 ELSE 4 END")
            ->orderBy('urutan')
            ->get();

        return response()->json(['data' => $pertanyaan]);
    }

    /**
     * GET /pertanyaan/{id}
     * Detail satu pertanyaan + opsinya (untuk form edit admin).
     */
    public function show($id)
    {
        $pertanyaan = Pertanyaan::with('opsiJawaban')
            ->where('pertanyaan_id', $id)
            ->firstOrFail();

        return response()->json(['data' => $pertanyaan]);
    }

    // ─────────────────────────────────────────────
    // PERTANYAAN — Create (admin only)
    // ─────────────────────────────────────────────

    /**
     * POST /pertanyaan
     * Body: { pertanyaan, aspek, urutan, opsi: [{label, teks, nilai}] }
     *
     * Opsi bersifat opsional saat create; bisa ditambah via
     * endpoint opsi tersendiri.
     */
    public function store(Request $request)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh menambah pertanyaan.'], 403);
        }

        $validated = $request->validate([
            'pertanyaan'         => ['required', 'string'],
            'aspek'              => ['required', Rule::in(['environment', 'social', 'governance'])],
            'urutan'             => ['required', 'integer', 'min:1'],
            'opsi'               => ['nullable', 'array', 'max:5'],
            'opsi.*.label'       => ['required', 'string', 'max:1'],
            'opsi.*.teks'        => ['required', 'string'],
            'opsi.*.nilai'       => ['required', 'integer', 'min:1', 'max:3'],
        ]);

        DB::beginTransaction();

        try {
            $pertanyaan = Pertanyaan::create([
                'pertanyaan' => $validated['pertanyaan'],
                'aspek'      => $validated['aspek'],
                'urutan'     => $validated['urutan'],
            ]);

            if (! empty($validated['opsi'])) {
                foreach ($validated['opsi'] as $opsi) {
                    $pertanyaan->opsiJawaban()->create($opsi);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'message' => 'Pertanyaan berhasil ditambahkan.',
            'data'    => $pertanyaan->load('opsiJawaban'),
        ], 201);
    }

    // ─────────────────────────────────────────────
    // PERTANYAAN — Update (admin only)
    // ─────────────────────────────────────────────

    /**
     * PUT /pertanyaan/{id}
     * Body: { pertanyaan?, aspek?, urutan? }
     * Hanya update field pertanyaan itu sendiri.
     * Untuk opsi, gunakan endpoint /pertanyaan/{id}/opsi.
     */
    public function update(Request $request, $id)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh mengubah pertanyaan.'], 403);
        }

        $pertanyaan = Pertanyaan::where('pertanyaan_id', $id)->firstOrFail();

        $validated = $request->validate([
            'pertanyaan' => ['sometimes', 'required', 'string'],
            'aspek'      => ['sometimes', 'required', Rule::in(['environment', 'social', 'governance'])],
            'urutan'     => ['sometimes', 'required', 'integer', 'min:1'],
        ]);

        $pertanyaan->update($validated);

        return response()->json([
            'message' => 'Pertanyaan berhasil diperbarui.',
            'data'    => $pertanyaan->load('opsiJawaban'),
        ]);
    }

    // ─────────────────────────────────────────────
    // PERTANYAAN — Delete (admin only)
    // ─────────────────────────────────────────────

    /**
     * DELETE /pertanyaan/{id}
     * Otomatis cascade delete semua opsi_jawaban-nya.
     */
    public function destroy(Request $request, $id)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh menghapus pertanyaan.'], 403);
        }

        $pertanyaan = Pertanyaan::where('pertanyaan_id', $id)->firstOrFail();
        $pertanyaan->delete();

        return response()->json(['message' => 'Pertanyaan berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────
    // OPSI JAWABAN — Create (admin only)
    // ─────────────────────────────────────────────

    /**
     * POST /pertanyaan/{id}/opsi
     * Body: { label, teks, nilai }
     */
    public function storeOpsi(Request $request, $id)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh menambah opsi.'], 403);
        }

        $pertanyaan = Pertanyaan::where('pertanyaan_id', $id)->firstOrFail();

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:1'],
            'teks'  => ['required', 'string'],
            'nilai' => ['required', 'integer', 'min:1', 'max:3'],
        ]);

        $opsi = $pertanyaan->opsiJawaban()->create($validated);

        return response()->json([
            'message' => 'Opsi berhasil ditambahkan.',
            'data'    => $opsi,
        ], 201);
    }

    // ─────────────────────────────────────────────
    // OPSI JAWABAN — Update (admin only)
    // ─────────────────────────────────────────────

    /**
     * PUT /pertanyaan/{id}/opsi/{opsiId}
     * Body: { label?, teks?, nilai? }
     */
    public function updateOpsi(Request $request, $id, $opsiId)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh mengubah opsi.'], 403);
        }

        // Pastikan opsi ini memang milik pertanyaan yang dimaksud
        $opsi = OpsiJawaban::where('opsi_id', $opsiId)
            ->where('pertanyaan_id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'label' => ['sometimes', 'required', 'string', 'max:1'],
            'teks'  => ['sometimes', 'required', 'string'],
            'nilai' => ['sometimes', 'required', 'integer', 'min:1', 'max:3'],
        ]);

        $opsi->update($validated);

        return response()->json([
            'message' => 'Opsi berhasil diperbarui.',
            'data'    => $opsi,
        ]);
    }

    // ─────────────────────────────────────────────
    // OPSI JAWABAN — Delete (admin only)
    // ─────────────────────────────────────────────

    /**
     * DELETE /pertanyaan/{id}/opsi/{opsiId}
     */
    public function destroyOpsi(Request $request, $id, $opsiId)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh menghapus opsi.'], 403);
        }

        $opsi = OpsiJawaban::where('opsi_id', $opsiId)
            ->where('pertanyaan_id', $id)
            ->firstOrFail();

        $opsi->delete();

        return response()->json(['message' => 'Opsi berhasil dihapus.']);
    }

    // ─────────────────────────────────────────────
    // OPSI JAWABAN — Sync bulk (admin only)
    // ─────────────────────────────────────────────

    /**
     * PUT /pertanyaan/{id}/opsi/sync
     * Ganti semua opsi sekaligus (berguna saat edit via form admin).
     * Body: { opsi: [{label, teks, nilai}] }
     */
    public function syncOpsi(Request $request, $id)
    {
        if (! $this->isAdmin($request)) {
            return response()->json(['message' => 'Hanya admin yang boleh menyinkronkan opsi.'], 403);
        }

        $pertanyaan = Pertanyaan::where('pertanyaan_id', $id)->firstOrFail();

        $validated = $request->validate([
            'opsi'         => ['required', 'array', 'min:1', 'max:5'],
            'opsi.*.label' => ['required', 'string', 'max:1'],
            'opsi.*.teks'  => ['required', 'string'],
            'opsi.*.nilai' => ['required', 'integer', 'min:1', 'max:3'],
        ]);

        DB::beginTransaction();

        try {
            // Hapus semua opsi lama, insert opsi baru
            $pertanyaan->opsiJawaban()->delete();

            foreach ($validated['opsi'] as $opsi) {
                $pertanyaan->opsiJawaban()->create($opsi);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'message' => 'Opsi berhasil disinkronkan.',
            'data'    => $pertanyaan->load('opsiJawaban'),
        ]);
    }
}
