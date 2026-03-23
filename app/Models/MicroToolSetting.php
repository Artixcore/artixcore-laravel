<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroToolSetting extends Model
{
    protected $fillable = [
        'micro_tool_id',
        'key',
        'value',
        'type',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(MicroTool::class, 'micro_tool_id');
    }
}
