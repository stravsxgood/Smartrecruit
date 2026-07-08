<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use App\Services\CvAnalyzeService;

class Application extends Model
{
    protected $fillable = [
        'applicant_profile_id',
        'job_position_id',
        'phone',
        'cv_path',
        'resume_original_name',
        'cover_letter',
        'parsed_data',
        'skills',
        'experience',
        'education',
        'portfolio_urls',
        'status',
        'custom_answers',
        'hrd_notes',
    ];

    protected function casts(): array
    {
        return [
            'custom_answers' => 'array',
            'parsed_data' => 'array',
            'skills' => 'array',
            'experience' => 'array',
            'education' => 'array',
            'portfolio_urls' => 'array',
        ];
    }

    // Event: Trigger analisis CV saat status berubah
    protected static function booted(): void
    {
        static::updated(function (Application $application) {
            // Hanya trigger jika status benar-benar berubah
            if ($application->wasChanged('status')) {
                $application->analyzeCv();
            }
        });
    }

    /**
     * Analisis CV menggunakan Gemini AI
     */
    public function analyzeCv(): void
    {
        // Cek apakah ada CV path
        if (!$this->cv_path) {
            Log::warning("CV Analysis skipped: No cv_path for application ID {$this->id}");
            return;
        }

        // Cek apakah file CV masih ada di storage
        $filePath = Storage::disk('public')->path($this->cv_path);

        if (!file_exists($filePath)) {
            Log::warning("CV Analysis skipped: File not found at {$filePath}");
            return;
        }

        try {
            // 1. Ekstrak teks dari PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $cvText = $pdf->getText();

            // 2. Validasi panjang teks
            if (mb_strlen($cvText) < 80) {
                Log::warning("CV Analysis skipped: Text too short ({$this->id})");
                return;
            }

            // 3. Panggil Gemini AI untuk analisis kualitas
            $service = new CvAnalyzeService();
            $analysis = $service->analyzeCvQuality($cvText);

            // 4. Simpan hasil analisis ke tabel cv_analyses
            $this->cvAnalysis()->updateOrCreate(
                ['application_id' => $this->id],
                [
                    'overall_score' => $analysis['overall_score'],
                    'skill_match' => $analysis['strengths'] ?? [],
                    'experience_match' => [],
                    'education_match' => [],
                    'recommendation' => $analysis['overall_score'] >= 70 ? 'Strong Match' :
                                       ($analysis['overall_score'] >= 50 ? 'Good Match' : 'Weak Match'),
                    'reasoning' => $analysis['summary'],
                    'raw_ai_response' => json_encode($analysis),
                ]
            );

            Log::info("CV Analysis completed for application ID {$this->id}. Score: {$analysis['overall_score']}");

        } catch (\Exception $e) {
            Log::error("CV Analysis failed for application ID {$this->id}: " . $e->getMessage());
        }
    }

    // Relasi ke ApplicantProfile
    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class);
    }

    // Relasi ke JobPosition
    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
    }

    // Relasi ke CvAnalysis
    public function cvAnalysis(): HasOne
    {
        return $this->hasOne(CvAnalysis::class);
    }
}
