<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmProject extends Model
{
    use SoftDeletes;

    public const STATUS_PLANNING = 'planning';

    public const STATUS_PROPOSAL = 'proposal';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_PLANNING,
        self::STATUS_PROPOSAL,
        self::STATUS_ACTIVE,
        self::STATUS_PAUSED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'contact_id',
        'title',
        'slug',
        'status',
        'service_type',
        'budget_amount',
        'currency',
        'start_date',
        'due_date',
        'completed_at',
        'description',
        'internal_notes',
        'assigned_to',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'budget_amount' => 'decimal:2',
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<CrmContact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
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
}
