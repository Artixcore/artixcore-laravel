<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestMicroToolUsage extends Model
{
    protected $table = 'guest_micro_tool_usage';

    protected $fillable = [
        'micro_tool_id',
        'guest_token',
        'session_id',
        'ip_address',
        'user_agent',
        'usage_date',
        'total_runs',
        'ads_shown_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MicroTool::class, 'micro_tool_id');
    }
}
