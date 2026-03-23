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
        'guest_token',
        'session_id',
        'request_ip',
        'request_hash',
        'input_summary',
        'result_summary',
        'status',
        'duration_ms',
        'error_code',
        'is_guest',
        'is_registered',
        'is_aid_user',
        'is_paid_user',
        'ads_shown',
        'is_saved',
        'source',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input_summary' => 'array',
            'is_guest' => 'boolean',
            'is_registered' => 'boolean',
            'is_aid_user' => 'boolean',
            'is_paid_user' => 'boolean',
            'ads_shown' => 'boolean',
            'is_saved' => 'boolean',
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
