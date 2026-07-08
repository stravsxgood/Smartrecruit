<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantDocument extends Model
{
    protected $fillable = [
        'applicant_profile_id',
        'original_name',
        'path',
        'file_type',
        'mime_type',
        'file_size',
        'disk',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class);
    }
}
