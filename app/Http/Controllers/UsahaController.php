<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Usaha;
use Illuminate\Http\Request;

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
                'dokumen.tanggal_registrasi'
            )
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
            'ktp' => ['required', 'string', 'max:255'],
            'npwp' => ['required', 'string', 'max:255'],
            'surat_izin_usaha' => ['required', 'string', 'max:255'],
        ]);

        $usaha = Usaha::create([
            'user_id' => $request->user()->user_id,
            'nama_usaha' => $validated['nama_usaha'],
            'bidang_usaha' => $validated['bidang_usaha'],
            'alamat' => $validated['alamat'],
        ]);

        $dokumen = Dokumen::create([
            'id_usaha' => $usaha->id_usaha,
            'ktp' => $validated['ktp'],
            'npwp' => $validated['npwp'],
            'surat_izin_usaha' => $validated['surat_izin_usaha'],
            'status_verifikasi' => 'menunggu',
            'tanggal_registrasi' => now()->toDateString(),
            'tanggal_verifikasi' => null,
        ]);

        return response()->json([
            'message' => 'Usaha berhasil ditambahkan dan dokumen dummy masuk verifikasi admin.',
            'data' => [
                'usaha' => $usaha,
                'dokumen' => $dokumen,
            ],
        ], 201);
    }
}
