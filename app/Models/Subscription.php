<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_PAST_DUE = 'past_due';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'plan_name',
        'product_key',
        'status',
        'billing_cycle',
        'starts_at',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActiveForPremium($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q): void {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
