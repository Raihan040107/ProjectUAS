<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Usaha;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsahaController extends Controller
{
    public function index(Request $request)
    {
        $usaha = Usaha::query()
            ->where('user_id', $request->user()->user_id)
            ->leftJoin('dokumen', 'usaha.id_usaha', '=', 'dokumen.id_usaha')
            ->select(
                'usaha.id_usaha',
                'usaha.nama_usaha',
                'usaha.bidang_usaha',
                'usaha.alamat',
                'dokumen.status_verifikasi',
                'dokumen.tanggal_registrasi',
                'dokumen.tanggal_verifikasi',
                'score_esg.skor_environmental',
                'score_esg.skor_social',
                'score_esg.skor_governance',
                'score_esg.skor_total',
                'score_esg.kategori_skor',
                'score_esg.tanggal_perhitungan'
            )
            ->leftJoin('score_esg', 'usaha.id_usaha', '=', 'score_esg.id_usaha')
            ->get();

        return response()->json([
            'data' => $usaha,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->id_role === 2) {
            return response()->json([
                'message' => 'Admin tidak perlu menambah usaha.',
            ], 403);
        }

        $validated = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:255'],
            'bidang_usaha' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string'],
            'ktp' => ['required'],
            'npwp' => ['required'],
            'surat_izin_usaha' => ['required'],
        ]);

        $usaha = Usaha::create([
            'user_id' => $request->user()->user_id,
            'nama_usaha' => $validated['nama_usaha'],
            'bidang_usaha' => $validated['bidang_usaha'],
            'alamat' => $validated['alamat'],
        ]);

        $dokumen = Dokumen::create([
            'id_usaha' => $usaha->id_usaha,
            'ktp' => $this->documentValue($request, 'ktp', $validated['ktp']),
            'npwp' => $this->documentValue($request, 'npwp', $validated['npwp']),
            'surat_izin_usaha' => $this->documentValue($request, 'surat_izin_usaha', $validated['surat_izin_usaha']),
            'status_verifikasi' => 'menunggu',
            'tanggal_registrasi' => now()->toDateString(),
            'tanggal_verifikasi' => null,
        ]);

        return response()->json([
            'message' => 'Usaha berhasil ditambahkan dan masuk antrean verifikasi admin.',
            'data' => [
                'usaha' => $usaha,
                'dokumen' => $dokumen,
            ],
        ], 201);
    }

    public function adminIndex(Request $request)
    {
        if ($request->user()->id_role !== 2) {
            return response()->json([
                'message' => 'Hanya admin yang boleh melihat data usaha.',
            ], 403);
        }

        $usaha = Usaha::query()
            ->join('users', 'usaha.user_id', '=', 'users.user_id')
            ->leftJoin('dokumen', 'usaha.id_usaha', '=', 'dokumen.id_usaha')
            ->leftJoin('score_esg', 'usaha.id_usaha', '=', 'score_esg.id_usaha')
            ->select(
                'usaha.id_usaha',
                'usaha.user_id',
                'usaha.nama_usaha',
                'usaha.bidang_usaha',
                'usaha.alamat',
                'users.nama as nama_user',
                'users.email as email_user',
                'dokumen.status_verifikasi',
                'dokumen.tanggal_registrasi',
                'dokumen.tanggal_verifikasi',
                'dokumen.ktp',
                'dokumen.npwp',
                'dokumen.surat_izin_usaha',
                'score_esg.skor_total',
                'score_esg.kategori_skor',
                'score_esg.tanggal_perhitungan'
            )
            ->orderByRaw("CASE dokumen.status_verifikasi WHEN 'menunggu' THEN 1 WHEN 'terverifikasi' THEN 2 WHEN 'ditolak' THEN 3 ELSE 4 END")
            ->orderByDesc('usaha.id_usaha')
            ->get();

        return response()->json([
            'data' => $usaha,
        ]);
    }

    public function verify(Request $request, int $id)
    {
        if ($request->user()->id_role !== 2) {
            return response()->json([
                'message' => 'Hanya admin yang boleh memverifikasi usaha.',
            ], 403);
        }

        $validated = $request->validate([
            'status_verifikasi' => ['required', Rule::in(['menunggu', 'terverifikasi', 'ditolak'])],
        ]);

        $dokumen = Dokumen::where('id_usaha', $id)->firstOrFail();
        $dokumen->update([
            'status_verifikasi' => $validated['status_verifikasi'],
            'tanggal_verifikasi' => $validated['status_verifikasi'] === 'menunggu'
                ? null
                : now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Status verifikasi usaha diperbarui.',
            'data' => $dokumen,
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        if ($request->user()->id_role !== 2) {
            return response()->json([
                'message' => 'Hanya admin yang boleh menghapus usaha.',
            ], 403);
        }

        $usaha = Usaha::findOrFail($id);

        // Hapus dokumen yang terkait
        Dokumen::where('id_usaha', $usaha->id_usaha)->delete();

        // Hapus usaha
        $usaha->delete();

        return response()->json([
            'message' => 'Usaha berhasil dihapus.',
        ]);
    }
    private function documentValue(Request $request, string $field, mixed $fallback): string
    {
        $file = $request->file($field);

        if ($file instanceof UploadedFile) {
            return $file->store("dokumen/{$field}", 'public') ?: $file->getClientOriginalName();
        }

        return trim((string) $fallback);
    }
}
