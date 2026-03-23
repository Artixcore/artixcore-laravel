<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroToolRunResult extends Model
{
    protected $fillable = [
        'micro_tool_run_id',
        'result_type',
        'payload',
        'warning_count',
        'error_count',
        'score',
        'is_exportable',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_exportable' => 'boolean',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(MicroToolRun::class, 'micro_tool_run_id');
    }
}
