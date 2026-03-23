<?php

namespace App\Services\Tools\Contracts;

use App\Models\User;

interface ToolHandlerInterface
{
    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(array $input, ?User $user): array;
}
