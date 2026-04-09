<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_LOST = 'lost';

    protected $fillable = [
        'source',
        'status',
        'name',
        'email',
        'phone',
        'company',
        'budget',
        'service_interest',
        'notes',
        'custom_fields',
        'conversation_summary',
        'internal_notes',
        'assigned_to',
        'ai_conversation_id',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<AiConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    /**
     * @return HasMany<AiConversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'lead_id');
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_QUALIFIED,
            self::STATUS_CONTACTED,
            self::STATUS_CONVERTED,
            self::STATUS_LOST,
        ];
    }
}
