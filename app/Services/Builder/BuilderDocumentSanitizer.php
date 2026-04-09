<?php

namespace App\Services\Builder;

use App\Support\Builder\BuilderNodeType;

class BuilderDocumentSanitizer
{
    public function __construct(
        private BuilderEmbedSanitizer $embedSanitizer,
    ) {}

    /**
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    public function sanitizeDocument(array $document): array
    {
        $root = $document['root'] ?? null;
        if (! is_array($root)) {
            return $document;
        }
        $document['root'] = $this->sanitizeNode($root);

        return $document;
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    private function sanitizeNode(array $node): array
    {
        $type = $node['type'] ?? '';
        $props = $node['props'] ?? [];
        if (! is_array($props)) {
            $props = [];
        }

        if ($type === BuilderNodeType::RichText->value && isset($props['html']) && is_string($props['html'])) {
            $props['html'] = $this->embedSanitizer->sanitizeHtml($props['html']);
        }

        if ($type === BuilderNodeType::Embed->value) {
            $props = $this->embedSanitizer->sanitizeEmbedProps($props);
        }

        $node['props'] = $props;

        $children = $node['children'] ?? [];
        if (is_array($children)) {
            $node['children'] = array_map(
                fn (array $c): array => $this->sanitizeNode($c),
                array_filter($children, 'is_array')
            );
        }

        return $node;
    }
}
