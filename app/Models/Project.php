<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = [
        'code', 'owner_id', 'name', 'description', 'domain', 'objective',
        'start_date', 'end_date', 'triage_answers', 'triage_total',
        'risk_level', 'mitigation_enabled',
    ];

    protected $casts = [
        'triage_answers'     => 'array',
        'start_date'         => 'date',
        'end_date'           => 'date',
        'mitigation_enabled' => 'boolean',
        'triage_total'       => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot(['role', 'weight'])
            ->withTimestamps();
    }

    public function experts(): HasMany
    {
        return $this->hasMany(ProjectMember::class)->where('role', 'expert');
    }

    public function expertWeightJudgments(): HasMany
    {
        return $this->hasMany(ExpertWeightJudgment::class);
    }

    public function formulations(): HasMany
    {
        return $this->hasMany(Formulation::class)->latest();
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class)->latest();
    }

    /** 9 faktor risiko sesuai domain + level proyek. */
    public function riskFactors()
    {
        return RiskFactor::forContext($this->domain, $this->risk_level)->get();
    }

    public function isOwner(?User $user): bool
    {
        return $user && $this->owner_id === $user->id;
    }

    public function hasMember(?User $user): bool
    {
        return $user && $this->members()->where('user_id', $user->id)->exists();
    }

    /** Apakah semua bobot expert sudah terisi (pembobotan sudah dilakukan). */
    public function hasExpertWeights(): bool
    {
        return $this->members()->whereNotNull('weight')->count() === $this->members()->count()
            && $this->members()->count() > 0;
    }
}
