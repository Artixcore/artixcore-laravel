<?php

namespace App\Services\Tools;

use App\Services\Tools\Contracts\ToolHandlerInterface;
use InvalidArgumentException;

class ToolRegistry
{
    /**
     * @param  array<string, class-string<ToolHandlerInterface>>  $map
     */
    public function __construct(private array $map) {}

    public function handlerForSlug(string $slug): ToolHandlerInterface
    {
        if (! isset($this->map[$slug])) {
            throw new InvalidArgumentException('Tool handler not registered.');
        }

        $class = $this->map[$slug];

        return app($class);
    }

    public function hasHandler(string $slug): bool
    {
        return isset($this->map[$slug]);
    }
}
