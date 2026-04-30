<?php

namespace App\Http\Controllers;

use App\Services\JawabanService;
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
            'id_usaha' => ['nullable', 'integer', 'exists:usaha,id_usaha'],
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*.pertanyaan_id' => ['required', 'integer', 'exists:pertanyaan,pertanyaan_id'],
            'jawaban.*.jawaban' => ['required', 'string', 'min:1'],
        ]);

        $result = $service->storeAndAnalyze(
            $user,
            $validated['jawaban'],
            $validated['id_usaha'] ?? null
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
