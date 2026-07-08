<?php

namespace App\Http\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ApplicantDocument;
use App\Models\ApplicantProfile;
use App\Services\CvAnalyzeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Smalot\PdfParser\Parser;

class ApplicantProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $profile = ApplicantProfile::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $profile->load([
            'documents' => fn($query) => $query->latest(),
        ]);

        return Inertia::render('ApplicantProfiles/Show', [
            'profile' => $profile,
            'files' => $this->profileFiles($profile),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'education' => ['nullable', 'array'],
            'education.*' => ['nullable', 'string', 'max:255'],

            'portfolio_urls' => ['nullable', 'array'],
            'portfolio_urls.*' => ['nullable', 'url', 'max:255'],

            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable', 'string', 'max:255'],

            'experience' => ['nullable', 'array'],
            'experience.*' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'user_id' => $request->user()->id,
        ];

        foreach (['education', 'portfolio_urls', 'skills', 'experience'] as $field) {
            if (
                array_key_exists($field, $validated) &&
                Schema::hasColumn('applicant_profiles', $field)
            ) {
                $data[$field] = array_values(array_filter($validated[$field] ?? []));
            }
        }

        $request->user()->applicantProfile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data,
        );

        return back()->with('success', 'Applicant profile berhasil disimpan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'education' => ['nullable', 'array'],
            'education.*' => ['nullable', 'string', 'max:255'],

            'portfolio_urls' => ['nullable', 'array'],
            'portfolio_urls.*' => ['nullable', 'url', 'max:255'],

            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable', 'string', 'max:255'],

            'experience' => ['nullable', 'array'],
            'experience.*' => ['nullable', 'string', 'max:255'],
        ]);

        $profile = $request->user()
            ->applicantProfile()
            ->whereKey($id)
            ->firstOrFail();

        $data = [];

        foreach (['education', 'portfolio_urls', 'skills', 'experience'] as $field) {
            if (
                array_key_exists($field, $validated) &&
                Schema::hasColumn('applicant_profiles', $field)
            ) {
                $data[$field] = array_values(array_filter($validated[$field] ?? []));
            }
        }

        $profile->update($data);

        return back()->with('success', 'Applicant profile berhasil diperbarui.');
    }

    public function uploadDocument(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'resume' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,png,jpg,jpeg,webp',
                'max:10240', // Max 10MB
            ],
        ]);

        $profile = ApplicantProfile::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $file = $validated['resume'];
        $extension = strtolower($file->getClientOriginalExtension());

        // 1. Simpan file fisik ke storage
        $path = $file->store(
            'applicant-documents/' . $request->user()->id,
            'public'
        );

        // 2. Simpan metadata dokumen ke tabel applicant_documents
        $profile->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'file_type' => $extension,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'disk' => 'public',
        ]);

        // ==========================================
        // 🚀 AUTO EXTRACT & PARSE VIA GEMINI AI (KHUSUS PDF)
        // ==========================================
        if ($extension === 'pdf') {
            try {
                $filePath = Storage::disk('public')->path($path);
                $parser = new Parser();
                $pdf = $parser->parseFile($filePath);
                $cvText = $pdf->getText();

                // Pastikan teks cukup panjang untuk dianalisis
                if (mb_strlen($cvText) >= 80) {
                    $service = new CvAnalyzeService();

                    // Panggil AI (Memakan waktu 3-8 detik)
                    $extractedData = $service->extractCvData($cvText);

                    // Update profile dengan data hasil AI
                    $profile->update([
                        'resume_path' => $path,
                        'resume_original_name' => $file->getClientOriginalName(),
                        'skills' => $extractedData['skills'] ?? [],
                        'experience' => $extractedData['experience'] ?? [],
                        'education' => $extractedData['education'] ?? [],
                    ]);
                }
            } catch (\Exception $e) {
                // Jika AI gagal (misal timeout atau PDF corrupt), log error tapi jangan gagalkan upload
                Log::error('CV AI Parsing Failed for User ' . $request->user()->id . ': ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('applicant-profiles.show')
            ->with('success', 'Dokumen berhasil diupload dan dianalisis oleh AI! Data profil Anda telah diperbarui.');
    }

    private function parseCVWithAffinda(ApplicantProfile $profile, string $path, UploadedFile $file): void
    {
        try {
            $response = Http::withToken(config('services.affinda.api_key'))
                ->timeout(60)
                ->attach(
                    'file',
                    Storage::disk('public')->get($path),
                    $file->getClientOriginalName()
                )
                ->post('https://api.affinda.com/v1/resumes', [
                    'wait' => 'true'
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                // Ekstrak skills
                $skills = collect($data['skills'] ?? [])
                    ->pluck('name')
                    ->filter()
                    ->values()
                    ->toArray();

                // Ekstrak experience
                $experience = collect($data['workExperience'] ?? [])
                    ->map(fn($exp) => [
                        'position' => $exp['jobTitle'] ?? null,
                        'company' => $exp['organization'] ?? null,
                        'start_date' => $exp['startDate'] ?? null,
                        'end_date' => $exp['endDate'] ?? null,
                        'description' => $exp['jobDescription'] ?? null,
                    ])
                    ->toArray();

                // Ekstrak education
                $education = collect($data['education'] ?? [])
                    ->map(fn($edu) => [
                        'degree' => $edu['degree'] ?? null,
                        'institution' => $edu['institution'] ?? null,
                        'graduation_date' => $edu['endDate'] ?? null,
                    ])
                    ->toArray();

                // Update profile dengan data parsed
                $profile->update([
                    'skills' => $skills,
                    'experience' => $experience,
                    'education' => $education,
                ]);

                Log::info('CV parsed successfully for user: ' . $profile->user_id);
            }
        } catch (\Exception $e) {
            Log::error('Affinda API Error: ' . $e->getMessage());
        }
    }

    public function deleteDocument(Request $request, ApplicantDocument $document): RedirectResponse
    {
        if ($document->applicantProfile->user_id !== $request->user()->id) {
            abort(403);
        }

        Storage::disk($document->disk ?? 'public')->delete($document->path);

        $document->delete();

        return redirect()
            ->route('applicant-profiles.show')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    private function profileFiles(ApplicantProfile $profile): array
    {
        return $profile->documents
            ->map(fn(ApplicantDocument $document) => [
                'id' => $document->id,
                'name' => $document->original_name,
                'file_type' => $document->file_type ?? pathinfo($document->path, PATHINFO_EXTENSION),
                'file_size' => $this->formatBytes($document->file_size ?? 0),
                'upload_date' => optional($document->created_at)->format('d M Y H:i'),
                'preview_url' => asset('storage/' . ltrim($document->path, '/')),
            ])
            ->values()
            ->all();
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 KB';
        }

        $kb = $bytes / 1024;

        if ($kb < 1024) {
            return number_format($kb, 1) . ' KB';
        }

        return number_format($kb / 1024, 2) . ' MB';
    }
}
