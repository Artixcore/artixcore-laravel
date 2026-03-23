<?php

namespace App\Observers;

use App\Models\MicroTool;
use App\Models\MicroToolStatusLog;

class MicroToolObserver
{
    public function creating(MicroTool $tool): void
    {
        if (auth()->check()) {
            if ($tool->created_by === null) {
                $tool->created_by = auth()->id();
            }
            $tool->updated_by = auth()->id();
        }
    }

    public function updating(MicroTool $tool): void
    {
        if (auth()->check()) {
            $tool->updated_by = auth()->id();
        }
    }

    public function created(MicroTool $tool): void
    {
        $this->log($tool, 'created', null, json_encode($tool->only(['slug', 'title', 'is_active'])));
    }

    public function updated(MicroTool $tool): void
    {
        if ($tool->wasChanged('is_active')) {
            $this->log(
                $tool,
                $tool->is_active ? 'enabled' : 'disabled',
                json_encode(['is_active' => (bool) $tool->getOriginal('is_active')]),
                json_encode(['is_active' => $tool->is_active]),
            );
        }

        if ($tool->wasChanged('micro_tool_category_id')) {
            $this->log(
                $tool,
                'category_changed',
                (string) $tool->getOriginal('micro_tool_category_id'),
                (string) $tool->micro_tool_category_id,
            );
        }

        if ($tool->wasChanged('access_type')) {
            $this->log(
                $tool,
                'access_changed',
                (string) $tool->getOriginal('access_type'),
                (string) $tool->access_type,
            );
        }

        if ($tool->wasChanged('ads_enabled')) {
            $this->log(
                $tool,
                'ads_changed',
                json_encode(['ads_enabled' => (bool) $tool->getOriginal('ads_enabled')]),
                json_encode(['ads_enabled' => $tool->ads_enabled]),
            );
        }
    }

    private function log(MicroTool $tool, string $action, ?string $old, ?string $new): void
    {
        MicroToolStatusLog::query()->create([
            'micro_tool_id' => $tool->id,
            'action' => $action,
            'old_value' => $old,
            'new_value' => $new,
            'note' => null,
            'changed_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
