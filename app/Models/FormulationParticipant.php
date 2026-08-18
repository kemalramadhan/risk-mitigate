<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulationParticipant extends Model
{
    protected $fillable = [
        'formulation_id', 'user_id', 'reference_factor_id',
        'weight', 'submitted_at', 'priority_vector',
    ];

    protected $casts = [
        'priority_vector' => 'array',
        'weight'          => 'float',
        'submitted_at'    => 'datetime',
    ];

    public function formulation(): BelongsTo
    {
        return $this->belongsTo(Formulation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referenceFactor(): BelongsTo
    {
        return $this->belongsTo(RiskFactor::class, 'reference_factor_id');
    }

    public function hasSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }
}
