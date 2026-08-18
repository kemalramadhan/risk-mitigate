<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrategyReference extends Model
{
    protected $fillable = [
        'mitigation_strategy_id', 'sumber', 'judul_sumber', 'lokasi', 'kutipan', 'position',
    ];

    public function strategy(): BelongsTo
    {
        return $this->belongsTo(MitigationStrategy::class, 'mitigation_strategy_id');
    }
}
