<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroSavedReport extends Model
{
    protected $fillable = [
        'user_id',
        'micro_tool_id',
        'micro_tool_run_id',
        'title',
    ];

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
