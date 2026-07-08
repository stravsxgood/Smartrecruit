<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'suspend_reason',
        'suspended_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }

    // ✅ Cek apakah user adalah Super Admin
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    // ✅ Cek apakah user adalah HRD
    public function isHrd(): bool
    {
        return $this->role === 'hrd';
    }

    // ✅ Cek apakah user adalah Candidate
    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    // ✅ Cek apakah user aktif
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ✅ Cek apakah user di-suspend
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // ✅ Suspend user
    public function suspend(?string $reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'suspend_reason' => $reason,
            'suspended_at' => now(),
        ]);
    }

    // ✅ Activate kembali user
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'suspend_reason' => null,
            'suspended_at' => null,
        ]);
    }

    // ✅ Soft delete user
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
        $this->delete(); // Soft delete
    }

    // ✅ Relasi ke job positions yang dibuat
    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class, 'created_by');
    }

    // ✅ Filament: Hanya super_admin dan hrd yang bisa akses admin panel
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['super_admin', 'hrd']) && $this->status === 'active';
    }
}
