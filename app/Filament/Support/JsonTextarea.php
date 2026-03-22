<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;

final class JsonTextarea
{
    public static function make(string $name, string $label = ''): Textarea
    {
        return Textarea::make($name)
            ->label($label ?: $name)
            ->rows(14)
            ->columnSpanFull()
            ->formatStateUsing(function ($state): string {
                if (is_array($state)) {
                    return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                }

                return (string) ($state ?? '{}');
            })
            ->dehydrateStateUsing(function ($state): array {
                if (is_array($state)) {
                    return $state;
                }
                $decoded = json_decode((string) $state, true);

                return is_array($decoded) ? $decoded : [];
            });
    }
}
