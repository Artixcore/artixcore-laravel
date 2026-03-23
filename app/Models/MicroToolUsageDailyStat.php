<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroToolUsageDailyStat extends Model
{
    protected $fillable = [
        'micro_tool_id',
        'stat_date',
        'total_runs',
        'guest_runs',
        'free_user_runs',
        'paid_user_runs',
        'success_runs',
        'failed_runs',
        'saved_reports_count',
        'ads_views_count',
        'unique_users_count',
        'unique_guests_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stat_date' => 'date',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MicroTool::class, 'micro_tool_id');
    }
}
