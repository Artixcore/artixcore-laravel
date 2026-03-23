<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroToolRunResult extends Model
{
    protected $fillable = [
        'micro_tool_run_id',
        'payload',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(MicroToolRun::class, 'micro_tool_run_id');
    }
}
