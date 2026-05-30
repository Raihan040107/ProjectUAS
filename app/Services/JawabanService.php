<?php

namespace App\Services;

use App\Models\Jawaban;
use App\Models\Usaha;
use App\Models\User;
use App\Services\Gemini\GeminiService;
use Illuminate\Support\Facades\DB;
use Throwable;

class JawabanService
{
    public function __construct(
        private GeminiService $gemini
    ) {}

    public function storeAndAnalyze(User $user, array $jawabanData, ?int $usahaId = null): array
    {
        $result = DB::transaction(function () use ($user, $jawabanData, $usahaId) {
            $usaha = $this->resolveUsaha($user, $usahaId);

            $questionIds = collect($jawabanData)
                ->pluck('pertanyaan_id')
                ->map(fn ($id) => (int) $id)
                ->values();

            DB::table('jawaban')
                ->where('id_usaha', $usaha->id_usaha)
                ->whereIn('pertanyaan_id', $questionIds)
                ->delete();

            DB::table('jawaban')->insert(
                collect($jawabanData)->map(fn (array $item) => [
                    'id_usaha' => $usaha->id_usaha,
                    'pertanyaan_id' => (int) $item['pertanyaan_id'],
                    'jawaban' => $item['jawaban'],
                ])->all()
            );

            $jawaban = Jawaban::query()
                ->where('id_usaha', $usaha->id_usaha)
                ->whereIn('pertanyaan_id', $questionIds)
                ->orderBy('pertanyaan_id')
                ->get();

            return [
                'usaha' => $usaha,
                'jawaban' => $jawaban,
            ];
        });

        $analysis = null;
        $aiSuccess = true;
        $aiMessage = 'Analisis AI berhasil';

        try {
            $analysis = $this->gemini->analyze($user, $result['usaha'], $jawabanData);
            $analysis = is_array($analysis) ? $this->withScoreCategory($analysis) : $analysis;
            $this->storeAnalysis($result['usaha']->id_usaha, $analysis);
        } catch (Throwable $exception) {
            report($exception);

            $aiSuccess = false;
            $aiMessage = $this->aiFailureMessage($exception);
        }

        return [
            'success' => true,
            'message' => $aiSuccess ? 'Jawaban tersimpan & dianalisis' : $aiMessage,
            'id_usaha' => $result['usaha']->id_usaha,
            'data' => $result['jawaban'],
            'ai_success' => $aiSuccess,
            'ai_message' => $aiMessage,
            'gemini_analysis' => $analysis,
        ];
    }
//USAHA DUMMY UNTUK JAWABAN YANG TIDAK MELAMPIRKAN ID USAHA, SEHINGGA ANALISIS AI MASIH BISA DISIMPAN DAN DITAMPILKAN DI HALAMAN ANALISIS TERBARU
    private function resolveUsaha(User $user, ?int $usahaId): Usaha
    {
        if ($usahaId) {
            $usaha = Usaha::where('user_id', $user->user_id)
                ->where('id_usaha', $usahaId)
                ->first();

            if ($usaha) {
                return $usaha;
            }
        }

        return Usaha::firstOrCreate(
            ['user_id' => $user->user_id],
            [
                'nama_usaha' => 'Usaha '.$user->nama,
                'bidang_usaha' => 'Belum diisi',
                'alamat' => 'Belum diisi',
            ]
        );
    }

    public function latestAnalysis(User $user, ?int $usahaId = null): ?array
    {
        $usahaQuery = Usaha::where('user_id', $user->user_id);
        $usaha = $usahaId
            ? (clone $usahaQuery)->where('id_usaha', $usahaId)->first()
            : $usahaQuery->first();

        if (! $usaha) {
            return null;
        }

        $dampak = DB::table('dampak')
            ->where('id_usaha', $usaha->id_usaha)
            ->first();
        $score = DB::table('score_esg')
            ->where('id_usaha', $usaha->id_usaha)
            ->first();
        $pengajuan = DB::table('pengajuan')
            ->where('id_usaha', $usaha->id_usaha)
            ->first();

        if (! $dampak && ! $score && ! $pengajuan) {
            return null;
        }

        if ($this->isBrokenStoredAnalysis($dampak, $score, $pengajuan)) {
            return null;
        }

        return [
            'usaha' => [
                'id_usaha' => $usaha->id_usaha,
                'nama_usaha' => $usaha->nama_usaha,
                'bidang_usaha' => $usaha->bidang_usaha,
                'alamat' => $usaha->alamat,
            ],
            'dampak' => [
                'lingkungan' => $dampak->dampak_lingkungan ?? null,
                'sosial' => $dampak->dampak_sosial ?? null,
                'ekonomi' => $dampak->dampak_ekonomi ?? null,
            ],
            'skor_esg' => [
                'lingkungan' => isset($score->skor_environmental) ? (float) $score->skor_environmental : null,
                'sosial' => isset($score->skor_social) ? (float) $score->skor_social : null,
                'governance' => isset($score->skor_governance) ? (float) $score->skor_governance : null,
                'total' => isset($score->skor_total) ? (float) $score->skor_total : null,
                'kategori' => $score->kategori_skor ?? null,
                'tanggal_perhitungan' => $score->tanggal_perhitungan ?? null,
            ],
            'rekomendasi' => $pengajuan->text_saran ?? null,
            'pengajuan' => [
                'jumlah_pinjaman' => isset($pengajuan->jumlah_pinjaman) ? (float) $pengajuan->jumlah_pinjaman : null,
                'tenor_bulanan' => isset($pengajuan->tenor_bulan) ? (int) $pengajuan->tenor_bulan : null,
                'tingkat_bunga_khusus' => isset($pengajuan->tingkat_bunga_khusus) ? (float) $pengajuan->tingkat_bunga_khusus : null,
                'syarat_esg' => $pengajuan->perubahan ?? null,
            ],
        ];
    }

    private function storeAnalysis(int $usahaId, mixed $analysis): void
    {
        if (! is_array($analysis)) {
            return;
        }

        $dampak = $analysis['dampak'] ?? [];
        $score = $analysis['skor_esg'] ?? [];
        $pengajuan = $analysis['pengajuan'] ?? [];
        $totalScore = $this->numberValue($score['total'] ?? 0);
        $previousScore = DB::table('score_esg')
            ->where('id_usaha', $usahaId)
            ->value('skor_total');

        DB::transaction(function () use ($usahaId, $analysis, $dampak, $score, $pengajuan, $totalScore, $previousScore) {
            DB::table('dampak')->updateOrInsert(
                ['id_usaha' => $usahaId],
                [
                    'dampak_lingkungan' => $this->textValue($dampak['lingkungan'] ?? null),
                    'dampak_sosial' => $this->textValue($dampak['sosial'] ?? null),
                    'dampak_ekonomi' => $this->textValue($dampak['ekonomi'] ?? null),
                ]
            );

            DB::table('score_esg')->updateOrInsert(
                ['id_usaha' => $usahaId],
                [
                    'skor_environmental' => $this->numberValue($score['lingkungan'] ?? 0),
                    'skor_social' => $this->numberValue($score['sosial'] ?? 0),
                    'skor_governance' => $this->numberValue($score['governance'] ?? 0),
                    'skor_total' => $totalScore,
                    'kategori_skor' => $this->scoreCategory($totalScore),
                    'tanggal_perhitungan' => now()->toDateString(),
                    'skor_lama' => $previousScore,
                    'skor_baru' => $totalScore,
                ]
            );

            $recommendation = $this->textValue($analysis['rekomendasi'] ?? null);
            $syaratEsg = $this->textValue($pengajuan['syarat_esg'] ?? null);

            DB::table('pengajuan')->updateOrInsert(
                ['id_usaha' => $usahaId],
                [
                    'perubahan' => substr($syaratEsg, 0, 255),
                    'text_saran' => $recommendation,
                    'jumlah_pinjaman' => $this->numberValue($pengajuan['jumlah_pinjaman'] ?? 0),
                    'tenor_bulan' => (int) $this->numberValue($pengajuan['tenor_bulanan'] ?? 0),
                    'bunga_diterapkan' => $this->numberValue($pengajuan['tingkat_bunga_khusus'] ?? 0),
                    'tingkat_bunga_khusus' => $this->numberValue($pengajuan['tingkat_bunga_khusus'] ?? 0),
                    'skor_esg_minimum' => (int) round($totalScore),
                ]
            );
        });
    }

    private function withScoreCategory(array $analysis): array
    {
        if (! isset($analysis['skor_esg']) || ! is_array($analysis['skor_esg'])) {
            $analysis['skor_esg'] = [];
        }

        $totalScore = $this->numberValue($analysis['skor_esg']['total'] ?? 0);
        $analysis['skor_esg']['kategori'] ??= $this->scoreCategory($totalScore);

        return $analysis;
    }

    private function textValue(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value) ?: '';
        }

        return trim((string) ($value ?? ''));
    }

    private function numberValue(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function scoreCategory(float $score): string
    {
        return match (true) {
            $score >= 80 => 'Sangat Baik',
            $score >= 60 => 'Baik',
            $score >= 40 => 'Cukup',
            default => 'Perlu Perbaikan',
        };
    }

    private function aiFailureMessage(Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        return match (true) {
            str_contains($message, 'bukan json valid'),
            str_contains($message, 'struktur json') => 'Jawaban tersimpan, tapi format analisis AI belum valid. Coba submit ulang.',
            str_contains($message, 'high demand') => 'Jawaban tersimpan, tapi Gemini sedang ramai. Coba submit ulang beberapa saat lagi.',
            str_contains($message, 'curl error 77') => 'Jawaban tersimpan, tapi konfigurasi SSL PHP untuk koneksi Gemini belum terbaca. Restart server Laravel/Laragon.',
            str_contains($message, 'api key'),
            str_contains($message, 'permission'),
            str_contains($message, 'unauthorized') => 'Jawaban tersimpan, tapi GEMINI_API_KEY ditolak atau belum valid.',
            str_contains($message, 'quota') => 'Jawaban tersimpan, tapi kuota Gemini API sedang habis atau dibatasi.',
            default => 'Jawaban tersimpan, tapi analisis AI gagal karena koneksi atau layanan Gemini.',
        };
    }

    private function isBrokenStoredAnalysis(mixed $dampak, mixed $score, mixed $pengajuan): bool
    {
        $lingkungan = trim((string) ($dampak->dampak_lingkungan ?? ''));
        $rekomendasi = trim((string) ($pengajuan->text_saran ?? ''));
        $totalScore = (float) ($score->skor_total ?? 0);
        $loanAmount = (float) ($pengajuan->jumlah_pinjaman ?? 0);

        return ($totalScore === 0.0 && $loanAmount === 0.0)
            && (str_starts_with($lingkungan, '{') || str_starts_with($rekomendasi, '{'));
    }
}
