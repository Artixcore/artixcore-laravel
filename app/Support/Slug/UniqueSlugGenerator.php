<?php

namespace App\Support\Slug;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UniqueSlugGenerator
{
    /**
     * Generate a URL-safe unique slug for a table column.
     */
    public function unique(string $table, string $column, string $base, ?int $ignoreId = null): string
    {
        $slug = $this->normalizeBase($base);
        if ($slug === '') {
            $slug = 'article';
        }

        $candidate = $slug;
        $suffix = 2;

        while ($this->exists($table, $column, $candidate, $ignoreId)) {
            $candidate = $slug.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function normalizeBase(string $base): string
    {
        $s = Str::slug($base);

        return Str::limit($s, 240, '');
    }

    private function exists(string $table, string $column, string $slug, ?int $ignoreId): bool
    {
        return DB::table($table)
            ->where($column, $slug)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }
}
