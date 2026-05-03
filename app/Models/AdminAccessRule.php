<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminAccessRule extends Model
{
    use SoftDeletes;

    public const AREA_ADMIN = 'admin';

    public const AREA_MASTER = 'master';

    public const AREA_BOTH = 'both';

    /**
     * @var list<string>
     */
    public static function guardAreas(): array
    {
        return [self::AREA_ADMIN, self::AREA_MASTER, self::AREA_BOTH];
    }

    protected $fillable = [
        'name',
        'guard_area',
        'ip_address',
        'cidr',
        'description',
        'is_active',
        'created_by',
        'updated_by',
        'last_matched_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_matched_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
