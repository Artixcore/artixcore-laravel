<?php

namespace App\Services\Builder;

class BuilderTreeManipulator
{
    /**
     * @param  array<string, mixed>  $document
     * @param  array<string, mixed>  $newNode
     * @return array<string, mixed>
     */
    public function replaceNodeById(array $document, string $targetId, array $newNode): array
    {
        $root = $document['root'] ?? null;
        if (! is_array($root)) {
            return $document;
        }

        if (($root['id'] ?? '') === $targetId) {
            $document['root'] = $newNode;

            return $document;
        }

        if ($this->replaceInChildren($root, $targetId, $newNode)) {
            $document['root'] = $root;
        }

        return $document;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function replaceInChildren(array &$node, string $targetId, array $newNode): bool
    {
        $children = &$node['children'];
        if (! is_array($children)) {
            return false;
        }

        foreach ($children as $i => $child) {
            if (! is_array($child)) {
                continue;
            }
            if (($child['id'] ?? '') === $targetId) {
                $children[$i] = $newNode;

                return true;
            }
            if ($this->replaceInChildren($children[$i], $targetId, $newNode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $document
     * @param  array<string, mixed>  $fragmentRoot  Single node to append under target parent
     * @return array<string, mixed>
     */
    public function appendChild(array $document, string $parentId, array $fragmentRoot): array
    {
        $root = $document['root'] ?? null;
        if (! is_array($root)) {
            return $document;
        }

        if (($root['id'] ?? '') === $parentId) {
            $root['children'] ??= [];
            if (is_array($root['children'])) {
                $root['children'][] = $fragmentRoot;
            }
            $document['root'] = $root;

            return $document;
        }

        if ($this->appendUnder($root, $parentId, $fragmentRoot)) {
            $document['root'] = $root;
        }

        return $document;
    }

    /**
     * @param  array<string, mixed>  $node
     * @param  array<string, mixed>  $fragmentRoot
     */
    private function appendUnder(array &$node, string $parentId, array $fragmentRoot): bool
    {
        if (($node['id'] ?? '') === $parentId) {
            $node['children'] ??= [];
            if (is_array($node['children'])) {
                $node['children'][] = $fragmentRoot;
            }

            return true;
        }

        $children = &$node['children'];
        if (! is_array($children)) {
            return false;
        }

        foreach ($children as $i => $child) {
            if (is_array($child) && $this->appendUnder($children[$i], $parentId, $fragmentRoot)) {
                return true;
            }
        }

        return false;
    }
}
