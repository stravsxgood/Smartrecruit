<?php

namespace App\Http\Controllers;

use App\Services\CvAnalyzeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Smalot\PdfParser\Parser;
use Throwable;

class CvAnalysisController extends Controller
{
    public function analyze(Request $request, CvAnalyzeService $cvAnalyzeService): RedirectResponse
    {
        $validated = $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $file = $validated['cv'];
        $path = $file->store('tmp/cv-uploads');

        try {
            $fullPath = Storage::path($path);
            $cvText = $this->extractPdfText($fullPath);

            if (mb_strlen($cvText) < 80) {
                return back()->withErrors([
                    'cv' => 'Teks CV terlalu pendek atau tidak terbaca. Gunakan PDF yang berisi teks, bukan hasil scan gambar.',
                ]);
            }

            $analysis = $cvAnalyzeService->analyzeCvQuality($cvText);

            return redirect()
                ->route('cv.result')
                ->with('cv_analysis_result', [
                    'fileName' => $file->getClientOriginalName(),
                    'analysis' => $analysis,
                ]);
        } catch (Throwable $exception) {
            report($exception);

            $message = $exception->getMessage();
            $lowerMessage = strtolower($message);

            if (
                str_contains($lowerMessage, 'quota') ||
                str_contains($lowerMessage, 'billing') ||
                str_contains($lowerMessage, '429') ||
                str_contains($lowerMessage, 'resource_exhausted')
            ) {
                return back()->withErrors([
                    'cv' => 'Quota Gemini API habis atau billing/limit Google belum aktif. Cek API key, quota, dan billing di Google AI Studio atau Google Cloud.',
                ]);
            }

            if (
                str_contains($lowerMessage, 'api key') ||
                str_contains($lowerMessage, 'permission') ||
                str_contains($lowerMessage, 'unauthorized') ||
                str_contains($lowerMessage, '403') ||
                str_contains($lowerMessage, '401')
            ) {
                return back()->withErrors([
                    'cv' => 'Gemini API key tidak valid atau tidak punya akses. Cek GEMINI_API_KEY di file .env.',
                ]);
            }

            return back()->withErrors([
                'cv' => 'Analisis CV gagal. Detail: ' . $message,
            ]);
        } finally {
            Storage::delete($path);
        }
    }

    public function show(): Response
    {
        $payload = session('cv_analysis_result');

        return Inertia::render('JobPositions/Show', [
            'mode' => 'cv-analysis',
            'fileName' => $payload['fileName'] ?? 'CV tidak ditemukan',
            'analysis' => $payload['analysis'] ?? [
                'overall_score' => 0,
                'summary' => 'Hasil analisis tidak tersedia. Silakan upload CV kembali.',
                'strengths' => [],
                'weaknesses' => [],
                'improvements' => [],
            ],
        ]);
    }

    private function extractPdfText(string $fullPath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);

        return trim($pdf->getText());
    }
}
