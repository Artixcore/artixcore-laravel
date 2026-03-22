<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AiRun extends Model
{
    protected $fillable = [
        'ai_workflow_id',
        'ai_agent_id',
        'correlation_id',
        'status',
        'input',
        'output',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AiRun $run): void {
            if (empty($run->correlation_id)) {
                $run->correlation_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return BelongsTo<AiWorkflow, $this>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AiWorkflow::class, 'ai_workflow_id');
    }

    /**
     * @return BelongsTo<AiAgent, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(AiAgent::class, 'ai_agent_id');
    }

    /**
     * @return HasMany<AiRunLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AiRunLog::class, 'ai_run_id')->orderBy('id');
    }
}
