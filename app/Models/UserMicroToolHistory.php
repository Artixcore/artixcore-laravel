<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMicroToolHistory extends Model
{
    protected $table = 'user_micro_tool_histories';

    protected $fillable = [
        'user_id',
        'micro_tool_id',
        'micro_tool_run_id',
        'title',
        'summary',
        'is_favorite',
        'is_saved',
        'viewed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'is_saved' => 'boolean',
            'viewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MicroTool::class, 'micro_tool_id');
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(MicroToolRun::class, 'micro_tool_run_id');
    }
}
