<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MitigationStrategy extends Model
{
    protected $fillable = [
        'risk_factor_id', 'strategy_text', 'position',
    ];

    public function riskFactor(): BelongsTo
    {
        return $this->belongsTo(RiskFactor::class);
    }

    public function references(): HasMany
    {
        return $this->hasMany(StrategyReference::class)->orderBy('position');
    }
}
