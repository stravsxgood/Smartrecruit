<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;

class CvAnalyzeService
{
    // ==========================================
    // 1. FUNGSI UNTUK ANALISIS KUALITAS (HRD)
    // ==========================================
    public function analyzeCvQuality(string $cvText): array
    {
        $cvText = trim($cvText);

        if ($cvText === '' || mb_strlen($cvText) < 80) {
            throw new RuntimeException('CV text is too short or unreadable.');
        }

        $aiResult = $this->requestGemini($cvText, $this->buildCvQualityPrompt($cvText), $this->geminiCvQualitySchema());

        return [
            'overall_score' => $this->normalizeScore(Arr::get($aiResult, 'overall_score')),
            'summary' => $this->normalizeText(Arr::get($aiResult, 'summary')),
            'strengths' => $this->normalizeStringArray(Arr::get($aiResult, 'strengths', [])),
            'weaknesses' => $this->normalizeStringArray(Arr::get($aiResult, 'weaknesses', [])),
            'improvements' => $this->normalizeStringArray(Arr::get($aiResult, 'improvements', [])),
        ];
    }

    // ==========================================
    // 2. FUNGSI BARU: PARSER DATA TERSTRUKTUR (USER PROFILE)
    // ==========================================
    public function extractCvData(string $cvText): array
    {
        $cvText = trim($cvText);

        if ($cvText === '' || mb_strlen($cvText) < 80) {
            throw new RuntimeException('CV text is too short or unreadable.');
        }

        $aiResult = $this->requestGemini($cvText, $this->buildCvExtractionPrompt($cvText), $this->geminiCvExtractionSchema());

        return [
            'skills' => $this->normalizeStringArray(Arr::get($aiResult, 'skills', [])),
            'experience' => $this->normalizeExperienceArray(Arr::get($aiResult, 'experience', [])),
            'education' => $this->normalizeEducationArray(Arr::get($aiResult, 'education', [])),
        ];
    }

    // ==========================================
    // CORE GEMINI REQUEST
    // ==========================================
    private function requestGemini(string $cvText, string $prompt, array $schema): array
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model');

        if (blank($apiKey)) throw new RuntimeException('GEMINI_API_KEY belum dikonfigurasi.');
        if (blank($model)) throw new RuntimeException('GEMINI_MODEL belum dikonfigurasi.');

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])
            ->acceptJson()
            ->asJson()
            ->timeout(90)
            ->retry(2, 700)
            ->post($endpoint, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $schema,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini API request gagal: ' . $response->body());
        }

        $content = $response->json('candidates.0.content.parts.0.text');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('Respons Gemini kosong.');
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Respons Gemini bukan JSON valid: ' . $exception->getMessage());
        }

        if (! is_array($decoded)) {
            throw new RuntimeException('Respons Gemini harus berupa object JSON.');
        }

        return $decoded;
    }

    // ==========================================
    // PROMPTS & SCHEMAS
    // ==========================================
    private function buildCvQualityPrompt(string $cvText): string
    {
        return <<<PROMPT
Kamu adalah reviewer CV profesional dan ATS specialist.
Analisis dan koreksi kualitas CV berikut.

CV TEXT:
{$cvText}

TUGAS:
Berikan review CV. Fokus pada struktur, kejelasan profil, kekuatan pengalaman, dan kelengkapan skill.
ATURAN:
- overall_score angka 0-100.
- Jangan mengarang data. Jawaban Bahasa Indonesia. Return JSON valid.
PROMPT;
    }

    private function geminiCvQualitySchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => ['overall_score', 'summary', 'strengths', 'weaknesses', 'improvements'],
            'properties' => [
                'overall_score' => ['type' => 'NUMBER'],
                'summary' => ['type' => 'STRING'],
                'strengths' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                'weaknesses' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                'improvements' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
            ],
        ];
    }

    private function buildCvExtractionPrompt(string $cvText): string
    {
        return <<<PROMPT
Kamu adalah CV parser profesional. Ekstrak data terstruktur dari CV berikut.

CV TEXT:
{$cvText}

TUGAS:
Ekstrak skills, experience, dan education.
ATURAN:
- Jika tanggal tidak jelas, gunakan string kosong.
- Jangan mengarang data. Return JSON valid.
PROMPT;
    }

    private function geminiCvExtractionSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'required' => ['skills', 'experience', 'education'],
            'properties' => [
                'skills' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                'experience' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'position' => ['type' => 'STRING'],
                            'company' => ['type' => 'STRING'],
                            'start_date' => ['type' => 'STRING'],
                            'end_date' => ['type' => 'STRING'],
                            'description' => ['type' => 'STRING'],
                        ],
                    ],
                ],
                'education' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'degree' => ['type' => 'STRING'],
                            'institution' => ['type' => 'STRING'],
                            'graduation_date' => ['type' => 'STRING'],
                            'field_of_study' => ['type' => 'STRING'],
                        ],
                    ],
                ],
            ],
        ];
    }

    // ==========================================
    // NORMALIZERS
    // ==========================================
    private function normalizeScore(mixed $value): float
    {
        $score = is_numeric($value) ? (float) $value : 0.0;
        return round(max(0, min(100, $score)), 2);
    }

    private function normalizeText(mixed $value): string
    {
        return is_string($value) ? trim($value) : (is_numeric($value) ? (string) $value : '');
    }

    private function normalizeStringArray(mixed $value): array
    {
        if (! is_array($value)) return [];
        return collect($value)->filter(fn($i) => is_string($i) || is_numeric($i))->map(fn($i) => trim((string) $i))->filter()->values()->all();
    }

    private function normalizeExperienceArray(mixed $value): array
    {
        if (! is_array($value)) return [];
        return collect($value)->filter(fn($i) => is_array($i))->map(function ($i) {
            return [
                'position' => isset($i['position']) ? trim((string)$i['position']) : null,
                'company' => isset($i['company']) ? trim((string)$i['company']) : null,
                'start_date' => isset($i['start_date']) ? trim((string)$i['start_date']) : null,
                'end_date' => isset($i['end_date']) ? trim((string)$i['end_date']) : null,
                'description' => isset($i['description']) ? trim((string)$i['description']) : null,
            ];
        })->filter(fn($i) => !empty($i['position']) || !empty($i['company']))->values()->all();
    }

    private function normalizeEducationArray(mixed $value): array
    {
        if (! is_array($value)) return [];
        return collect($value)->filter(fn($i) => is_array($i))->map(function ($i) {
            return [
                'degree' => isset($i['degree']) ? trim((string)$i['degree']) : null,
                'institution' => isset($i['institution']) ? trim((string)$i['institution']) : null,
                'graduation_date' => isset($i['graduation_date']) ? trim((string)$i['graduation_date']) : null,
                'field_of_study' => isset($i['field_of_study']) ? trim((string)$i['field_of_study']) : null,
            ];
        })->filter(fn($i) => !empty($i['degree']) || !empty($i['institution']))->values()->all();
    }
}
