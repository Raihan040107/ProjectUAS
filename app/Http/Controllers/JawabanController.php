<?php

namespace App\Http\Controllers;

use App\Services\JawabanService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class JawabanController extends Controller
{
    public function store(Request $request, JawabanService $service)
    {
        $user = $request->user();

        if ($user->id_role === 2) {
            return response()->json([
                'message' => 'Admin tidak perlu mengisi jawaban.',
            ], 403);
        }

        $validated = $request->validate([
            'id_usaha' => ['required', 'integer', 'exists:usaha,id_usaha'],
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*.pertanyaan_id' => ['required', 'integer', 'distinct', 'exists:pertanyaan,pertanyaan_id'],
            'jawaban.*.jawaban' => ['required', 'string', 'min:1'],
        ]);

        $requiredQuestionIds = DB::table('pertanyaan')
            ->pluck('pertanyaan_id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $answeredQuestionIds = collect($validated['jawaban'])
            ->pluck('pertanyaan_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();
        $missingQuestionIds = array_values(array_diff($requiredQuestionIds, $answeredQuestionIds));

        if (! empty($missingQuestionIds)) {
            return response()->json([
                'message' => 'Semua pertanyaan ESG wajib dijawab sebelum dianalisis.',
                'missing_pertanyaan_id' => $missingQuestionIds,
            ], 422);
        }

        $usaha = DB::table('usaha')
            ->leftJoin('dokumen', 'usaha.id_usaha', '=', 'dokumen.id_usaha')
            ->where('usaha.id_usaha', $validated['id_usaha'])
            ->where('usaha.user_id', $user->user_id)
            ->select('dokumen.status_verifikasi')
            ->first();

        if (! $usaha) {
            return response()->json([
                'message' => 'Usaha tidak ditemukan untuk akun ini.',
            ], 404);
        }

        if ($usaha->status_verifikasi !== 'terverifikasi') {
            return response()->json([
                'message' => 'Usaha harus diverifikasi admin sebelum evaluasi ESG.',
            ], 403);
        }

        $result = $service->storeAndAnalyze(
            $user,
            $validated['jawaban'],
            $validated['id_usaha']
        );

        return response()->json($result, 201);
    }

    public function analysis(Request $request, JawabanService $service)
    {
        $validated = $request->validate([
            'id_usaha' => ['nullable', 'integer', 'exists:usaha,id_usaha'],
        ]);

        return response()->json([
            'data' => $service->latestAnalysis($request->user(), $validated['id_usaha'] ?? null),
        ]);
    }
}
