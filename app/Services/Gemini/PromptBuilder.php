<?php

namespace App\Services\Gemini;

use App\Models\Pertanyaan;
use App\Models\User;
use App\Models\Usaha;

class PromptBuilder
{
    public function build(User $user, Usaha $usaha, array $jawabanData): string
    {
        $ids = array_column($jawabanData, 'pertanyaan_id');

        $pertanyaanMap = Pertanyaan::with('opsiJawaban')
            ->whereIn('pertanyaan_id', $ids)
            ->get()
            ->keyBy('pertanyaan_id');

        $prompt = <<<PROMPT
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

BUSINESS DATA:
Nama Usaha: {$usaha->nama_usaha}
Bidang Usaha: {$usaha->bidang_usaha}
Alamat/Lokasi: {$usaha->alamat}

INTERVIEW DATA:
PROMPT;

        foreach ($jawabanData as $i => $item) {
            $q = $pertanyaanMap[$item['pertanyaan_id']] ?? null;
            $answer = trim((string) $item['jawaban']);
            $selectedLabel = $this->selectedLabel($answer);
            $selectedOption = $q && $selectedLabel
                ? $q->opsiJawaban->firstWhere('label', $selectedLabel)
                : null;

            $prompt .= ($i + 1) . ". Q: " . ($q->pertanyaan ?? 'Unknown') . "\n";
            $prompt .= "Aspek: " . ($q->aspek ?? 'unknown') . "\n";
            $prompt .= "A: {$answer}\n";

            if ($selectedOption) {
                $prompt .= "Nilai opsi DB: {$selectedOption->nilai} dari 3\n";
            }

            $prompt .= "\n";
        }

        $prompt .= <<<PROMPT
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
- Use BUSINESS DATA, INTERVIEW DATA, and Nilai opsi DB as the main evidence
- If data unclear, assume conservative estimation
- Do NOT hallucinate facts outside input
PROMPT;

        return $prompt;
    }

    private function selectedLabel(string $answer): ?string
    {
        if (preg_match('/^\s*([A-Z])\s*\./i', $answer, $matches)) {
            return strtoupper($matches[1]);
        }

        return null;
    }
}
