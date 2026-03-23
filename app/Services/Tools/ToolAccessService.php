<?php

namespace App\Services\Tools;

use App\Models\MicroTool;
use App\Models\User;

class ToolAccessResolution
{
    public function __construct(
        public bool $canExecute,
        public bool $lockedForCatalog,
        public bool $adsExpected,
        public bool $adFree,
        public ?string $blockReason = null,
    ) {}
}

class ToolAccessService
{
    public function resolveForCatalog(MicroTool $tool, ?User $user): ToolAccessResolution
    {
        return $this->resolve($tool, $user);
    }

    public function resolveForRun(MicroTool $tool, ?User $user): ToolAccessResolution
    {
        return $this->resolve($tool, $user);
    }

    private function resolve(MicroTool $tool, ?User $user): ToolAccessResolution
    {
        $registered = $user !== null;
        $paid = $user?->isCurrentlyPremium() ?? false;

        if (! $tool->is_active) {
            return new ToolAccessResolution(
                canExecute: false,
                lockedForCatalog: true,
                adsExpected: false,
                adFree: false,
                blockReason: 'This tool is not available.',
            );
        }

        $access = $tool->access_type ?? 'public';

        $needsLogin = $tool->requires_auth
            || in_array($access, ['registered', 'premium'], true)
            || ($access === 'mixed' && $tool->requires_auth);

        $needsPremium = (bool) $tool->is_premium
            || $access === 'premium'
            || ($access === 'mixed' && (bool) $tool->is_premium);

        $canExecute = true;

        if ($access === 'registered' && ! $registered) {
            $canExecute = false;
        }

        if (! $registered && $needsLogin) {
            $canExecute = false;
        }

        if ($needsPremium && ! $paid) {
            $canExecute = false;
        }

        $adsExpected = (bool) $tool->ads_enabled && ! $registered;
        $adFree = ! $adsExpected;

        $blockReason = null;
        if (! $canExecute) {
            $blockReason = match (true) {
                ! $registered && ($access === 'registered' || $needsLogin) => 'Sign in to use this tool.',
                $needsPremium && ! $paid => 'A premium subscription is required for this tool.',
                default => 'You cannot use this tool with your current account.',
            };
        }

        return new ToolAccessResolution(
            canExecute: $canExecute,
            lockedForCatalog: ! $canExecute,
            adsExpected: $adsExpected,
            adFree: $adFree,
            blockReason: $blockReason,
        );
    }
}
