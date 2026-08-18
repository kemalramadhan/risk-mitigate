<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulationJudgment extends Model
{
    protected $fillable = [
        'formulation_id', 'user_id', 'reference_factor_id', 'other_factor_id', 'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function formulation(): BelongsTo
    {
        return $this->belongsTo(Formulation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
