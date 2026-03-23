<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroToolAccessPlan extends Model
{
    public const PLAN_GUEST = 'guest';

    public const PLAN_FREE = 'free';

    public const PLAN_REGISTERED = 'registered';

    public const PLAN_PREMIUM = 'premium';

    public const PLAN_ENTERPRISE = 'enterprise';

    protected $fillable = [
        'micro_tool_id',
        'plan_type',
        'usage_limit_daily',
        'usage_limit_monthly',
        'ads_enabled',
        'export_enabled',
        'saved_history_enabled',
        'priority_queue_enabled',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ads_enabled' => 'boolean',
            'export_enabled' => 'boolean',
            'saved_history_enabled' => 'boolean',
            'priority_queue_enabled' => 'boolean',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MicroTool::class, 'micro_tool_id');
    }
}
