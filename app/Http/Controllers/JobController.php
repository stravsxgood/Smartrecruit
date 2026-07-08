<?php

namespace App\Http\Controllers;

use App\Models\ApplicantProfile;
use App\Models\Application;
use App\Models\JobPosition;
use App\Models\CvAnalysis;
use App\Services\CvAnalyzeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Smalot\PdfParser\Parser;

class JobController extends Controller
{
    public function index(): Response
    {
        $positions = JobPosition::with('creator')
            ->where('status', 'active')
            ->latest()
            ->get();

        $premiumImages = [
            'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=800',
            'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800',
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800',
            'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&q=80&w=800',
        ];

        $mappedJobs = $positions->map(function ($item, $index) use ($premiumImages) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'company' => $item->creator ? $item->creator->name : 'SmartRecruit Corp',
                'location' => 'Indonesia',
                'type' => 'Full-time',
                'description' => $item->description,
                'skills' => is_array($item->requirements) ? $item->requirements : ['Professional Skill'],
                'image_url' => $item->image
                    ? asset('storage/' . $item->image)
                    : $premiumImages[$index % count($premiumImages)],
            ];
        });

        $userDocuments = [];
        if (Auth::check()) {
            $profile = \App\Models\ApplicantProfile::where('user_id', Auth::id())->first();
            if ($profile) {
                $userDocuments = $profile->documents()
                    ->where('file_type', 'pdf')
                    ->latest()
                    ->get()
                    ->map(fn($doc) => [
                        'id' => $doc->id,
                        'name' => $doc->original_name,
                        'path' => $doc->path,
                        'upload_date' => $doc->created_at->format('d M Y'),
                        'file_size' => number_format($doc->file_size / 1024, 1) . ' KB',
                    ])
                    ->toArray();
            }
        }

        return Inertia::render('JobApply', [
            'jobs' => $mappedJobs,
            'userDocuments' => $userDocuments,
        ]);
    }

    public function show(JobPosition $job): Response
    {
        $job->load('creator');

        // Load user's documents jika sudah login
        $userDocuments = [];
        if (Auth::check()) {
            $profile = ApplicantProfile::where('user_id', Auth::id())->first();
            if ($profile) {
                $userDocuments = $profile->documents()
                    ->where('file_type', 'pdf')
                    ->latest()
                    ->get()
                    ->map(fn($doc) => [
                        'id' => $doc->id,
                        'name' => $doc->original_name,
                        'path' => $doc->path,
                        'upload_date' => $doc->created_at->format('d M Y'),
                    ])
                    ->toArray();
            }
        }

        return Inertia::render('Jobs/Show', [
            'job' => $job,
            'userDocuments' => $userDocuments,
        ]);
    }

    public function apply(Request $request, JobPosition $jobPosition)
    {
        // 1. Validasi input dari Modal Vue
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'cover_letter' => 'nullable|string|max:5000',
            'document_id' => 'nullable|exists:applicant_documents,id', // ID dokumen yang dipilih
        ]);

        // 2. Pastikan user punya ApplicantProfile
        $profile = ApplicantProfile::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        // 3. Cegah duplikasi lamaran
        $existing = Application::where('job_position_id', $jobPosition->id)
            ->where('applicant_profile_id', $profile->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melamar posisi ini sebelumnya.');
        }

        // 4. Tentukan CV yang akan digunakan
        $selectedDocument = null;
        $cvPath = $profile->resume_path;
        $resumeOriginalName = $profile->resume_original_name;
        $skills = $profile->skills ?? [];
        $experience = $profile->experience ?? [];
        $education = $profile->education ?? [];

        if (isset($validated['document_id'])) {
            // User memilih dokumen spesifik
            $selectedDocument = $profile->documents()->find($validated['document_id']);
            if ($selectedDocument) {
                $cvPath = $selectedDocument->path;
                $resumeOriginalName = $selectedDocument->original_name;

                // Jika dokumen punya parsed_data, gunakan itu
                if ($selectedDocument->parsed_data) {
                    $parsedData = json_decode($selectedDocument->parsed_data, true);
                    $skills = $parsedData['skills'] ?? $skills;
                    $experience = $parsedData['experience'] ?? $experience;
                    $education = $parsedData['education'] ?? $education;
                }
            }
        }

        // 5. Simpan ke database
        $application = Application::create([
            'applicant_profile_id' => $profile->id,
            'job_position_id'      => $jobPosition->id,
            'phone'                => $validated['phone'] ?? null,
            'cover_letter'         => $validated['cover_letter'] ?? null,
            'cv_path'              => $cvPath,
            'resume_original_name' => $resumeOriginalName,
            'skills'               => $skills,
            'experience'           => $experience,
            'education'            => $education,
            'portfolio_urls'       => $profile->portfolio_urls ?? [],
            'status'               => 'pending',
            'custom_answers'       => [],
        ]);

        // 6. Otomatis analisis CV jika ada CV path
        if ($cvPath) {
            $this->triggerAutoAnalysis($application);
        }

        return back()->with('success', 'Lamaran berhasil dikirim! Tim HRD akan segera meninjau profil Anda.');
    }

    /**
     * Method helper untuk otomatis analisis CV via Gemini AI
     */
    private function triggerAutoAnalysis(Application $application): void
    {
        try {
            $filePath = Storage::disk('public')->path($application->cv_path);

            if (!file_exists($filePath)) {
                return;
            }

            // Ekstrak teks dari PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $cvText = $pdf->getText();

            // Validasi panjang teks
            if (mb_strlen($cvText) < 80) {
                return;
            }

            // Panggil Gemini AI
            $service = new CvAnalyzeService();
            $analysis = $service->analyzeCvQuality($cvText);

            // Simpan hasil ke tabel cv_analyses
            CvAnalysis::create([
                'application_id'    => $application->id,
                'overall_score'     => $analysis['overall_score'],
                'skill_match'       => $analysis['strengths'] ?? [],
                'experience_match'  => [],
                'education_match'   => [],
                'recommendation'    => $analysis['overall_score'] >= 70 ? 'Strong Match' : ($analysis['overall_score'] >= 50 ? 'Good Match' : 'Weak Match'),
                'reasoning'         => $analysis['summary'],
                'raw_ai_response'   => json_encode($analysis),
            ]);
        } catch (\Exception $e) {
            Log::error('Auto CV Analysis Failed for Application ID ' . $application->id . ': ' . $e->getMessage());
        }
    }
}
