<?php

namespace App\Support\Ai;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AiPayloadTermSynchronizer
{
    /**
     * Sync categories/subcategories/tags from Ali JSON payload onto any termable model.
     */
    public static function sync(Model $model, array $payload): void
    {
        $ids = [];

        $categoryTax = Taxonomy::query()->firstOrCreate(['slug' => 'categories'], ['name' => 'Categories']);
        $tagsTax = Taxonomy::query()->firstOrCreate(['slug' => 'tags'], ['name' => 'Tags']);

        $parentCategory = null;
        $catName = trim((string) ($payload['category'] ?? ''));
        if ($catName !== '') {
            $parentCategory = Term::query()->firstOrCreate(
                ['taxonomy_id' => $categoryTax->id, 'slug' => Str::slug($catName)],
                ['name' => $catName, 'sort_order' => 0, 'parent_id' => null]
            );
            $ids[] = $parentCategory->id;
        }

        $subName = trim((string) ($payload['subcategory'] ?? ''));
        if ($subName !== '' && $parentCategory !== null) {
            $child = Term::query()->updateOrCreate(
                [
                    'taxonomy_id' => $categoryTax->id,
                    'parent_id' => $parentCategory->id,
                    'slug' => Str::slug($subName),
                ],
                ['name' => $subName, 'sort_order' => 0]
            );
            $ids[] = $child->id;
        }

        $tags = $payload['tags'] ?? [];
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $name = trim((string) $tag);
                if ($name === '') {
                    continue;
                }
                $term = Term::query()->firstOrCreate(
                    ['taxonomy_id' => $tagsTax->id, 'slug' => Str::slug($name)],
                    ['name' => $name, 'sort_order' => 0, 'parent_id' => null]
                );
                $ids[] = $term->id;
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));
        if ($ids !== []) {
            $model->terms()->sync($ids);
        }
    }
}
