<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiWorkflowStep extends Model
{
    protected $fillable = [
        'ai_workflow_id',
        'sort_order',
        'name',
        'action_type',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'config' => 'array',
        ];
    }

    /**
     * @return BelongsTo<AiWorkflow, $this>
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AiWorkflow::class, 'ai_workflow_id');
    }
}
