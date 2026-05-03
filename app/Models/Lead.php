<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_ARCHIVED = 'archived';

    /** @deprecated Prefer STATUS_REJECTED for new records; retained for legacy AI/intake rows */
    public const STATUS_LOST = 'lost';

    /**
     * Service options for the public lead form (display strings stored as-is).
     *
     * @var list<string>
     */
    public const SERVICE_TYPES = [
        'Web Development',
        'App Development',
        'SaaS Development',
        'AI Software Development',
        'E-commerce Development',
        'CRM / ERP Development',
        'UI/UX Design',
        'Automation',
        'API Development',
        'Cloud / DevOps',
        'Maintenance & Support',
        'Other',
    ];

    protected $fillable = [
        'source',
        'status',
        'name',
        'email',
        'phone',
        'company',
        'budget',
        'service_interest',
        'service_type',
        'notes',
        'message',
        'ip_address',
        'user_agent',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'custom_fields',
        'visitor_context',
        'conversation_summary',
        'internal_notes',
        'admin_notes',
        'assigned_to',
        'ai_conversation_id',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
            'visitor_context' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePipelineNew(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_CONTACTED,
            self::STATUS_QUALIFIED,
            self::STATUS_CONVERTED,
            self::STATUS_REJECTED,
            self::STATUS_ARCHIVED,
            self::STATUS_LOST,
        ];
    }
}
