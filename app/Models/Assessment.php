<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    /** Urutan level risiko yang dinilai pada tiap assessment. */
    public const LEVELS = ['Low', 'Medium', 'High', 'Critical'];

    protected $fillable = [
        'project_id', 'created_by', 'status', 'computed_at',
    ];

    protected $casts = [
        'computed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AssessmentParticipant::class);
    }

    public function assessors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assessment_participants')->withTimestamps();
    }

    public function formulations(): HasMany
    {
        return $this->hasMany(Formulation::class);
    }

    public function formulationForLevel(string $level): ?Formulation
    {
        return $this->formulations->firstWhere('risk_level', $level);
    }

    public function isParticipant(?User $user): bool
    {
        return $user && $this->participants()->where('user_id', $user->id)->exists();
    }

    /** 1 penilai -> AHP standar; >1 -> AHP-Express + agregasi grup. */
    public function isStandard(): bool
    {
        return $this->participants()->count() === 1;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /** Jumlah level yang hasilnya sudah dihitung. */
    public function computedLevelCount(): int
    {
        return $this->formulations->where('status', 'completed')->count();
    }

    public function allComputed(): bool
    {
        return $this->formulations->count() === count(self::LEVELS)
            && $this->formulations->every(fn ($f) => $f->status === 'completed');
    }
}
