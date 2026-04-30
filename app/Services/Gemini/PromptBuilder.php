<?php

namespace App\Services\Gemini;

use App\Models\Pertanyaan;
use App\Models\User;

class PromptBuilder
{
    public function build(User $user, array $jawabanData): string
    {
        $ids = array_column($jawabanData, 'pertanyaan_id');

        $pertanyaanMap = Pertanyaan::whereIn('pertanyaan_id', $ids)
            ->get()
            ->keyBy('pertanyaan_id');

        $prompt = "
ROLE: ESG Financial & Business Risk Analyst AI

TASK:
Analisis jawaban kandidat untuk menilai:
1. Dampak usaha terhadap ESG (Environment, Social, Governance)
2. Skor ESG (0-100)
3. Rekomendasi kelayakan usaha
4. Parameter pengajuan pinjaman

USER DATA:
Nama: {$user->nama}
Email: {$user->email}

INTERVIEW DATA:
";

        foreach ($jawabanData as $i => $item) {
            $q = $pertanyaanMap[$item['pertanyaan_id']] ?? null;

            $prompt .= ($i + 1) . ". Q: " . ($q->pertanyaan ?? 'Unknown') . "\n";
            $prompt .= "A: {$item['jawaban']}\n\n";
        }

        $prompt .= "
OUTPUT FORMAT (STRICT JSON ONLY):
{
  \"dampak\": {
    \"lingkungan\": \"maks 1 kalimat\",
    \"sosial\": \"maks 1 kalimat\",
    \"ekonomi\": \"maks 1 kalimat\"
  },
  \"skor_esg\": {
    \"lingkungan\": number,
    \"sosial\": number,
    \"governance\": number,
    \"total\": number
  },
  \"rekomendasi\": \"maks 2 kalimat\",
  \"pengajuan\": {
    \"jumlah_pinjaman\": number,
    \"tenor_bulanan\": number,
    \"tingkat_bunga_khusus\": number,
    \"syarat_esg\": \"maks 1 kalimat\"
  }
}

RULES:
- ONLY return valid JSON
- NO markdown
- NO explanation text
- Keep every string concise, under 220 characters
- ALL scores must be 0-100
- TOTAL = average of ESG scores
- If data unclear, assume conservative estimation
- Do NOT hallucinate facts outside input
";

        return $prompt;
    }
}
