<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvAnalysis extends Model
{
    protected $fillable = [
        'application_id',
        'overall_score',
        'skill_match',
        'experience_match',
        'education_match',
        'recommendation',
        'reasoning',
        'raw_ai_response',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'skill_match' => 'array',
            'experience_match' => 'array',
            'education_match' => 'array',
            'raw_ai_response' => 'array',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
