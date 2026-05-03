<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmContact extends Model
{
    use SoftDeletes;

    public const TYPE_LEAD = 'lead';

    public const TYPE_PROSPECT = 'prospect';

    public const TYPE_CLIENT = 'client';

    public const TYPE_PARTNER = 'partner';

    public const TYPE_VENDOR = 'vendor';

    public const TYPE_OTHER = 'other';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_LEAD,
        self::TYPE_PROSPECT,
        self::TYPE_CLIENT,
        self::TYPE_PARTNER,
        self::TYPE_VENDOR,
        self::TYPE_OTHER,
    ];

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_PROPOSAL_SENT = 'proposal_sent';

    public const STATUS_WON = 'won';

    public const STATUS_LOST = 'lost';

    public const STATUS_ACTIVE_CLIENT = 'active_client';

    public const STATUS_INACTIVE_CLIENT = 'inactive_client';

    public const STATUS_ARCHIVED = 'archived';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CONTACTED,
        self::STATUS_QUALIFIED,
        self::STATUS_PROPOSAL_SENT,
        self::STATUS_WON,
        self::STATUS_LOST,
        self::STATUS_ACTIVE_CLIENT,
        self::STATUS_INACTIVE_CLIENT,
        self::STATUS_ARCHIVED,
    ];

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    /** @var list<string> */
    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    protected $fillable = [
        'type',
        'status',
        'name',
        'email',
        'phone',
        'company_name',
        'job_title',
        'website',
        'source_id',
        'source_detail',
        'service_interest',
        'service_id',
        'saas_platform_id',
        'project_id',
        'industry',
        'company_size',
        'budget_range',
        'priority',
        'geo_country',
        'geo_region',
        'geo_city',
        'geo_postal',
        'geo_latitude',
        'geo_longitude',
        'ip_address',
        'user_agent',
        'notes',
        'last_contacted_at',
        'next_follow_up_at',
        'assigned_to',
        'converted_at',
        'created_by',
        'updated_by',
        'lead_id',
    ];

    protected function casts(): array
    {
        return [
            'geo_latitude' => 'decimal:7',
            'geo_longitude' => 'decimal:7',
            'last_contacted_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<CrmSource, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(CrmSource::class, 'source_id');
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function saasPlatform(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'saas_platform_id');
    }

    /**
     * @return BelongsTo<CrmProject, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(CrmProject::class, 'project_id');
    }

    /**
     * @return BelongsTo<Lead, $this>
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
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
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return HasMany<CrmContactNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(CrmContactNote::class, 'contact_id')->orderByDesc('created_at');
    }

    /**
     * @param  Builder<CrmContact>  $query
     * @return Builder<CrmContact>
     */
    public function scopeClients(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereIn('type', [self::TYPE_CLIENT, self::TYPE_PARTNER])
                ->orWhereIn('status', [self::STATUS_ACTIVE_CLIENT, self::STATUS_WON]);
        });
    }
}
