<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MicroToolRun extends Model
{
    protected $fillable = [
        'micro_tool_id',
        'user_id',
        'guest_key',
        'input_summary',
        'status',
        'duration_ms',
        'error_code',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_summary' => 'array',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MicroTool::class, 'micro_tool_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(MicroToolRunResult::class, 'micro_tool_run_id');
    }
}
