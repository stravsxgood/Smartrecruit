<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosition extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'description',
        'image',
        'status',
        'requirements',
        'benefits',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'benefits' => 'array',
        ];
    }

    // Relasi ke User (HRD/Admin yang membuat lowongan)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Applications (Daftar pelamar untuk lowongan ini)
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
