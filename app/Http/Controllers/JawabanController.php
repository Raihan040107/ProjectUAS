<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Usaha;
use Illuminate\Http\Request;

class JawabanController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->id_role === 2) {
            return response()->json([
                'message' => 'Admin tidak perlu mengisi jawaban.',
            ], 403);
        }

        $validated = $request->validate([
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*.pertanyaan_id' => ['required', 'integer', 'exists:pertanyaan,pertanyaan_id'],
            'jawaban.*.jawaban' => ['required', 'string'],
        ]);

        // INI BELUM JADI KARNA DASHBOARD ISI USAHA BELUM JADI, JADI SEMENTARA KITA ANGGAP SAJA SETIAP USER HANYA MEMILIKI 1 USAHA
        $usaha = Usaha::firstOrCreate(
            ['user_id' => $user->user_id],
            [
                'nama_usaha' => 'Usaha '.$user->nama,
                'bidang_usaha' => 'Belum diisi',
                'alamat' => 'Belum diisi',
            ]
        );

        $jawaban = collect($validated['jawaban'])->map(function (array $item) use ($usaha) {
            return Jawaban::updateOrCreate(
                [
                    'id_usaha' => $usaha->id_usaha,
                    'pertanyaan_id' => $item['pertanyaan_id'],
                ],
                ['jawaban' => $item['jawaban']]
            );
        })->values();

        return response()->json([
            'message' => 'Jawaban berhasil dikirim',
            'data' => $jawaban,
        ], 201);
    }
}
