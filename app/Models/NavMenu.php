<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavMenu extends Model
{
    protected $fillable = ['key', 'name'];

    /**
     * @return HasMany<NavItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(NavItem::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * @return HasMany<NavItem, $this>
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(NavItem::class)->orderBy('sort_order');
    }
}
