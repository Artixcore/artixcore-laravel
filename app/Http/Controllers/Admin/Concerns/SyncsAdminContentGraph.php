<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\Content\ContentRelationWriter;
use Illuminate\Database\Eloquent\Model;

trait SyncsAdminContentGraph
{
    /**
     * @param  list<int|string>  $ids
     */
    protected function syncMorphPivotOrdered(Model $owner, string $relation, array $ids): void
    {
        $clean = array_values(array_unique(array_filter(array_map(static fn ($id): int => (int) $id, $ids), static fn (int $id): bool => $id > 0)));
        $pivot = [];
        foreach ($clean as $i => $id) {
            $pivot[$id] = ['sort_order' => $i];
        }
        $owner->{$relation}()->sync($pivot);
    }

    /**
     * @param  list<int|string>  $ids
     */
    protected function syncOutgoingRelationIds(Model $source, string $relationType, string $relatedClass, array $ids): void
    {
        ContentRelationWriter::syncOutgoing($source, $relationType, $relatedClass, array_map(static fn ($id): int => (int) $id, $ids));
    }
}
