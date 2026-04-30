<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Pertanyaan::all(),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->id_role !== 2) {
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
            'data' => $pertanyaan,
        ], 201);
    }
}
