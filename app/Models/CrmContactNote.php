<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmContactNote extends Model
{
    public const TYPE_NOTE = 'note';

    public const TYPE_CALL = 'call';

    public const TYPE_EMAIL = 'email';

    public const TYPE_MEETING = 'meeting';

    public const TYPE_STATUS_CHANGE = 'status_change';

    public const TYPE_PROJECT_UPDATE = 'project_update';

    public const TYPE_SYSTEM = 'system';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_NOTE,
        self::TYPE_CALL,
        self::TYPE_EMAIL,
        self::TYPE_MEETING,
        self::TYPE_STATUS_CHANGE,
        self::TYPE_PROJECT_UPDATE,
        self::TYPE_SYSTEM,
    ];

    protected $fillable = [
        'contact_id',
        'user_id',
        'type',
        'title',
        'body',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
