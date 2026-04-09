<?php

namespace App\Support\Builder;

use InvalidArgumentException;

class BuilderDocumentValidator
{
    public const MAX_DEPTH = 24;

    public const MAX_NODES = 600;

    public function __construct(
        private int $maxDepth = self::MAX_DEPTH,
        private int $maxNodes = self::MAX_NODES,
    ) {}

    /**
     * @param  array<string, mixed>  $document
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $document): void
    {
        if (($document['schemaVersion'] ?? null) !== BuilderDocumentDefaults::SCHEMA_VERSION) {
            throw new InvalidArgumentException('Unsupported document schemaVersion.');
        }

        $root = $document['root'] ?? null;
        if (! is_array($root)) {
            throw new InvalidArgumentException('Document root is missing or invalid.');
        }

        $total = $this->countNodes($document);
        if ($total > $this->maxNodes) {
            throw new InvalidArgumentException('Document exceeds maximum node count.');
        }

        $this->validateNode($root, 1);
    }

    /**
     * @param  array<string, mixed>  $node
     *
     * @throws InvalidArgumentException
     */
    public function validateNode(array $node, int $depth): void
    {
        if ($depth > $this->maxDepth) {
            throw new InvalidArgumentException('Document exceeds maximum nesting depth.');
        }

        $id = $node['id'] ?? null;
        if (! is_string($id) || $id === '') {
            throw new InvalidArgumentException('Each node requires a non-empty string id.');
        }

        $type = $node['type'] ?? null;
        if (! is_string($type) || ! BuilderNodeType::isValid($type)) {
            throw new InvalidArgumentException('Invalid or unknown node type: '.(is_string($type) ? $type : 'null'));
        }

        $enum = BuilderNodeType::from($type);
        $version = $node['version'] ?? null;
        if (! is_int($version) || $version < 1) {
            throw new InvalidArgumentException('Each node requires a positive integer version.');
        }

        $props = $node['props'] ?? null;
        if ($props !== null && ! is_array($props)) {
            throw new InvalidArgumentException('Node props must be an object/array when present.');
        }

        $children = $node['children'] ?? [];
        if (! is_array($children)) {
            throw new InvalidArgumentException('Node children must be an array.');
        }

        if ($children !== [] && ! $enum->allowsChildren()) {
            throw new InvalidArgumentException("Node type {$type} may not have children.");
        }

        if ($children === [] && in_array($type, [BuilderNodeType::Columns->value, BuilderNodeType::Column->value], true) && $depth > 1) {
            // columns/column may be empty while editing
        }

        $responsive = $node['responsive'] ?? null;
        if ($responsive !== null && ! is_array($responsive)) {
            throw new InvalidArgumentException('Node responsive must be an object/array when present.');
        }

        $visibility = $node['visibility'] ?? null;
        if ($visibility !== null && ! is_array($visibility)) {
            throw new InvalidArgumentException('Node visibility must be an object/array when present.');
        }

        foreach ($children as $child) {
            if (! is_array($child)) {
                throw new InvalidArgumentException('Each child must be an object.');
            }
            $this->validateNode($child, $depth + 1);
        }
    }

    /**
     * Count nodes without throwing (for guards).
     */
    public function countNodes(array $document): int
    {
        $root = $document['root'] ?? null;
        if (! is_array($root)) {
            return 0;
        }

        return $this->countNodeRecursive($root);
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function countNodeRecursive(array $node): int
    {
        $n = 1;
        $children = $node['children'] ?? [];
        if (! is_array($children)) {
            return $n;
        }
        foreach ($children as $child) {
            if (is_array($child)) {
                $n += $this->countNodeRecursive($child);
            }
        }

        return $n;
    }
}
