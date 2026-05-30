<?php

namespace App\Services\Gemini;

use Composer\CaBundle\CaBundle;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiClient
{
    private string $baseUrl;
    private string $key;
    private array $models;

    public function __construct()
    {
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
        $this->key = (string) config('services.gemini.key', '');
        $this->models = [
            'gemini-flash-latest',
            'gemini-flash-lite-latest',
            'gemini-2.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-3-flash-preview',
        ];
    }

    public function generate(string $prompt): array
    {
        if ($this->key === '') {
            throw new RuntimeException('GEMINI_API_KEY belum diatur.');
        }

        $lastMessage = 'Gemini API mengembalikan error.';

        foreach ($this->models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $response = Http::timeout(30)
                    ->acceptJson()
                    ->withOptions(['verify' => $this->caBundlePath()])
                    ->post($this->urlFor($model), [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2,
                            'maxOutputTokens' => 4096,
                            'responseMimeType' => 'application/json',
                        ],
                    ]);

                if ($response->successful()) {
                    return $response->json() ?? [];
                }

                $lastMessage = $response->json('error.message') ?? $lastMessage;

                if ($this->shouldTryNextModel($response->status(), $lastMessage)) {
                    break;
                }

                if (! $this->shouldRetry($response->status(), $lastMessage)) {
                    throw new RuntimeException($lastMessage);
                }

                usleep(300000);
            }
        }

        throw new RuntimeException($lastMessage);
    }

    private function urlFor(string $model): string
    {
        return "{$this->baseUrl}/{$model}:generateContent?key={$this->key}";
    }

    private function caBundlePath(): string
    {
        $configuredPath = trim((string) config('services.gemini.ca_bundle', ''));

        if ($configuredPath !== '') {
            return $configuredPath;
        }

        return CaBundle::getSystemCaRootBundlePath();
    }

    private function shouldRetry(int $status, string $message): bool
    {
        return in_array($status, [429, 500, 502, 503, 504], true)
            || str_contains(strtolower($message), 'high demand');
    }

    private function shouldTryNextModel(int $status, string $message): bool
    {
        $message = strtolower($message);

        return $status === 404
            || str_contains($message, 'not found')
            || str_contains($message, 'is not supported')
            || str_contains($message, 'not supported');
    }
}
