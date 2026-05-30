<?php

namespace App\Services\Gemini;

use App\Models\User;
use App\Models\Usaha;
use RuntimeException;

class GeminiService
{
    public function __construct(
        private PromptBuilder $promptBuilder,
        private GeminiClient $client
    ) {}

    public function analyze(User $user, Usaha $usaha, array $jawabanData): array
    {
        $prompt = $this->promptBuilder->build($user, $usaha, $jawabanData);

        $response = $this->client->generate($prompt);

        return $this->parseResponse($response);
    }

    private function parseResponse(array $response): array
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $finishReason = $response['candidates'][0]['finishReason'] ?? 'UNKNOWN';

        if (trim($text) === '') {
            throw new RuntimeException("Gemini tidak mengembalikan teks analisis. Finish reason: {$finishReason}.");
        }

        $json = $this->decodeJson($text);

        if (! $json) {
            throw new RuntimeException("Respons Gemini bukan JSON valid. Finish reason: {$finishReason}.");
        }

        return $this->normalizeAnalysis($json);
    }

    private function decodeJson(string $text): ?array
    {
        $clean = (string) preg_replace('/```(?:json)?|```/i', '', $text);

        foreach ([$clean, $this->extractJsonObject($clean)] as $candidate) {
            if (! $candidate) {
                continue;
            }

            $json = json_decode(trim($candidate), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json;
            }
        }

        return null;
    }

    private function extractJsonObject(string $text): ?string
    {
        $start = strpos($text, '{');

        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;
        $length = strlen($text);

        for ($i = $start; $i < $length; $i++) {
            $char = $text[$i];

            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($char === '"') {
                $inString = true;
                continue;
            }

            if ($char === '{') {
                $depth++;
                continue;
            }

            if ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    return substr($text, $start, $i - $start + 1);
                }
            }
        }

        return null;
    }

    private function normalizeAnalysis(array $analysis): array
    {
        if (! isset($analysis['dampak'], $analysis['skor_esg'], $analysis['pengajuan'])) {
            throw new RuntimeException('Struktur JSON Gemini tidak sesuai format analisis ESG.');
        }

        $dampak = is_array($analysis['dampak']) ? $analysis['dampak'] : [];
        $score = is_array($analysis['skor_esg']) ? $analysis['skor_esg'] : [];
        $pengajuan = is_array($analysis['pengajuan']) ? $analysis['pengajuan'] : [];

        return [
            'dampak' => [
                'lingkungan' => $this->stringValue($dampak['lingkungan'] ?? ''),
                'sosial' => $this->stringValue($dampak['sosial'] ?? ''),
                'ekonomi' => $this->stringValue($dampak['ekonomi'] ?? ''),
            ],
            'skor_esg' => [
                'lingkungan' => $this->scoreValue($score['lingkungan'] ?? 0),
                'sosial' => $this->scoreValue($score['sosial'] ?? 0),
                'governance' => $this->scoreValue($score['governance'] ?? 0),
                'total' => $this->scoreValue($score['total'] ?? 0),
            ],
            'rekomendasi' => $this->stringValue($analysis['rekomendasi'] ?? ''),
            'pengajuan' => [
                'jumlah_pinjaman' => $this->positiveNumber($pengajuan['jumlah_pinjaman'] ?? 0),
                'tenor_bulanan' => (int) $this->positiveNumber($pengajuan['tenor_bulanan'] ?? 0),
                'tingkat_bunga_khusus' => $this->positiveNumber($pengajuan['tingkat_bunga_khusus'] ?? 0),
                'syarat_esg' => $this->stringValue($pengajuan['syarat_esg'] ?? ''),
            ],
        ];
    }

    private function stringValue(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value) ?: '';
        }

        return trim((string) $value);
    }

    private function scoreValue(mixed $value): float
    {
        return min(100, max(0, $this->positiveNumber($value)));
    }

    private function positiveNumber(mixed $value): float
    {
        return is_numeric($value) ? max(0, (float) $value) : 0.0;
    }
}
