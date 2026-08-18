<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertWeightJudgment extends Model
{
    protected $fillable = [
        'project_id', 'reference_user_id', 'other_user_id', 'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function referenceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reference_user_id');
    }

    public function otherUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'other_user_id');
    }
}
