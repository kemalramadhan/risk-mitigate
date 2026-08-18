<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiskFactor extends Model
{
    protected $fillable = [
        'domain', 'risk_level', 'name', 'base_name', 'display_name', 'description', 'position',
    ];

    public function strategies(): HasMany
    {
        return $this->hasMany(MitigationStrategy::class)->orderBy('position');
    }

    /** Ambil 9 faktor risiko untuk kombinasi domain + level, terurut. */
    public function scopeForContext($query, string $domain, string $riskLevel)
    {
        return $query->where('domain', $domain)
            ->where('risk_level', $riskLevel)
            ->orderBy('position');
    }
}
