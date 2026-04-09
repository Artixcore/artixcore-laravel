<?php

namespace App\Support\Builder;

use Illuminate\Support\Str;

class BuilderDocumentCloner
{
    /**
     * Deep-clone document JSON with fresh UUIDs on every node.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    public function cloneWithNewIds(array $document): array
    {
        $out = $document;
        $root = $document['root'] ?? null;
        if (is_array($root)) {
            $out['root'] = $this->cloneNode($root);
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    public function cloneNode(array $node): array
    {
        $node['id'] = (string) Str::uuid();
        $children = $node['children'] ?? [];
        if (is_array($children)) {
            $node['children'] = array_map(fn (array $c): array => $this->cloneNode($c), $children);
        }

        return $node;
    }
}
