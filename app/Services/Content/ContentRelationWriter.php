<?php

namespace App\Services\Content;

use App\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;

class ContentRelationWriter
{
    /**
     * Replace all outgoing edges of this type from $source.
     *
     * @param  list<int>  $relatedIds
     */
    public static function syncOutgoing(
        Model $source,
        string $relationType,
        string $relatedClass,
        array $relatedIds,
    ): void {
        ContentRelation::query()
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->where('related_type', $relatedClass)
            ->where('relation_type', $relationType)
            ->delete();

        foreach (array_values(array_unique(array_filter($relatedIds, fn ($id): bool => $id > 0))) as $order => $relatedId) {
            ContentRelation::query()->create([
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
                'related_type' => $relatedClass,
                'related_id' => $relatedId,
                'relation_type' => $relationType,
                'sort_order' => $order,
                'is_featured' => false,
            ]);
        }
    }
}
