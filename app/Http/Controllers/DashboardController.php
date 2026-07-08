<?php

namespace App\Http\Controllers;

use App\Models\ApplicantProfile;
use App\Models\Application;
use App\Models\CvAnalysis;
use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Jika user adalah HRD atau Super Admin, tampilkan dashboard admin
        if (in_array($user->role, ['hrd', 'super_admin'])) {
            return $this->adminDashboard();
        }

        // Jika user adalah Candidate, tampilkan dashboard kandidat
        return $this->candidateDashboard($user);
    }

    /**
     * Dashboard untuk HRD dan Super Admin
     */
    private function adminDashboard(): Response
    {
        return Inertia::render('Dashboard', [
            'userRole' => 'admin',
            'stats' => [
                'job_positions' => JobPosition::count(),
                'applicants' => ApplicantProfile::count(),
                'applications' => Application::count(),
                'analyses' => CvAnalysis::count(),
            ],
            'recentApplications' => Application::with([
                'applicantProfile.user',
                'jobPosition',
                'cvAnalysis',
            ])
                ->latest()
                ->take(8)
                ->get()
                ->map(fn($app) => [
                    'id' => $app->id,
                    'applicant_name' => $app->applicantProfile?->user?->name ?? 'Unknown',
                    'company_name' => $app->jobPosition?->creator?->name ?? 'SmartRecruit Corp',
                    'company' => $app->jobPosition?->creator?->name ?? 'SmartRecruit Corp',
                    'job_title' => $app->jobPosition?->title ?? 'N/A',
                    'position' => $app->jobPosition?->title ?? 'N/A',
                    'status' => $app->status,
                    'overall_score' => $app->cvAnalysis?->overall_score,
                    'match_score' => $app->cvAnalysis?->overall_score,
                    'ai_match_score' => $app->cvAnalysis?->overall_score,
                    'applied_at' => $app->created_at->toIso8601String(),
                    'summary' => $app->cvAnalysis?->reasoning,
                    'notes' => $app->hrd_notes,
                    'cv_file_name' => $app->resume_original_name,
                    'cvFileName' => $app->resume_original_name,
                    'cv_url' => $app->cv_path ? asset('storage/' . $app->cv_path) : null,
                    'cvUrl' => $app->cv_path ? asset('storage/' . $app->cv_path) : null,
                ]),
            'jobPositions' => JobPosition::withCount('applications')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($jp) => [
                    'id' => $jp->id,
                    'title' => $jp->title,
                    'status' => $jp->status,
                    'applications_count' => $jp->applications_count,
                    'created_at' => $jp->created_at->toIso8601String(),
                ]),
        ]);
    }

    /**
     * Dashboard untuk Candidate (hanya melihat data miliknya sendiri)
     */
    private function candidateDashboard(User $user): Response
    {
        // 1. Ambil profile milik user yang sedang login SAJA
        $profile = ApplicantProfile::where('user_id', $user->id)->first();

        // 2. Jika profile belum ada, return data kosong
        if (!$profile) {
            return Inertia::render('Dashboard', [
                'userRole' => 'candidate',
                'applications' => [],
                'stats' => [
                    'total_applied' => 0,
                    'pending' => 0,
                    'reviewed' => 0,
                    'accepted' => 0,
                    'rejected' => 0,
                ],
                'profile' => null,
            ]);
        }

        // 3. ✅ SCOPING: Hanya ambil aplikasi milik profile ini
        $applications = Application::where('applicant_profile_id', $profile->id)
            ->with(['jobPosition.creator', 'cvAnalysis'])
            ->latest()
            ->get();

        // 4. Hitung statistik khusus user ini
        $stats = [
            'total_applied' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'reviewed' => $applications->where('status', 'reviewed')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        // 5. ✅ PERBAIKAN: Kirim field yang sesuai dengan Vue
        return Inertia::render('Dashboard', [
            'userRole' => 'candidate',
            'applications' => $applications->map(fn($app) => [
                'id' => $app->id,
                'job_title' => $app->jobPosition?->title ?? 'Lowongan Dihapus',
                'position' => $app->jobPosition?->title ?? 'Lowongan Dihapus',
                'company' => $app->jobPosition?->creator?->name ?? 'SmartRecruit Corp',
                'company_name' => $app->jobPosition?->creator?->name ?? 'SmartRecruit Corp',
                'applied_date' =>$app->created_at->format('d M Y'),
                'status' => $app->status,
                'overall_score' => $app->cvAnalysis?->overall_score,
                'match_score' => $app->cvAnalysis?->overall_score,
                'ai_match_score' => $app->cvAnalysis?->overall_score,
                'ai_score' => $app->cvAnalysis?->overall_score,
                'cover_letter' => $app->cover_letter,
                'summary' => $app->cvAnalysis?->reasoning,
                'notes' => $app->hrd_notes,
                'cv_file_name' => $app->resume_original_name,
                'cvFileName' => $app->resume_original_name,
                'cv_url' => $app->cv_path ? asset('storage/' . $app->cv_path) : null,
                'cvUrl' => $app->cv_path ? asset('storage/' . $app->cv_path) : null,
            ]),
            'stats' => $stats,
            'profile' => [
                'skills' => $profile->skills ?? [],
                'experience' => $profile->experience ?? [],
                'education' => $profile->education ?? [],
                'resume_path' => $profile->resume_path,
            ],
        ]);
    }
}
