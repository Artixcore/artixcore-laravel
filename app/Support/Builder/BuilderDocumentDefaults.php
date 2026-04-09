<?php

namespace App\Support\Builder;

use Illuminate\Support\Str;

class BuilderDocumentDefaults
{
    public const SCHEMA_VERSION = 1;

    /**
     * @return array{schemaVersion: int, root: array<string, mixed>}
     */
    public static function emptyDocument(): array
    {
        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'root' => self::emptyRootNode(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function emptyRootNode(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => BuilderNodeType::Root->value,
            'version' => 1,
            'props' => [],
            'children' => [],
            'responsive' => null,
            'visibility' => null,
        ];
    }
}
