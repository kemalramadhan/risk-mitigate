<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formulation extends Model
{
    protected $fillable = [
        'project_id', 'assessment_id', 'created_by', 'status', 'method',
        'risk_level', 'computed_at', 'result',
    ];

    protected $casts = [
        'result'      => 'array',
        'computed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(FormulationParticipant::class);
    }

    public function judgments(): HasMany
    {
        return $this->hasMany(FormulationJudgment::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mode kalkulasi: proyek dengan 1 partisipan memakai AHP standar
     * (pairwise penuh); lebih dari 1 memakai AHP-Express + agregasi grup.
     */
    public function isStandard(): bool
    {
        return $this->participants()->count() === 1;
    }

    /** Apakah seluruh partisipan telah submit judgment. */
    public function allSubmitted(): bool
    {
        $total = $this->participants()->count();

        return $total > 0
            && $this->participants()->whereNotNull('submitted_at')->count() === $total;
    }
}
