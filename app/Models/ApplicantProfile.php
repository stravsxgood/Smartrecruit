<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicantProfile extends Model
{
    protected $fillable = [
        'user_id',
        'resume_path',
        'resume_original_name',
        'resume_file_type',
        'resume_mime_type',
        'resume_file_size',
        'resume_disk',
        'skills',
        'experience',
        'education',
        'portfolio_urls',
    ];

    protected function casts(): array
    {
        return[
       'skills' => 'array',
        'experience' => 'array',
        'education' => 'array',
        'portfolio_urls' => 'array',
        'resume_file_size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }
}
