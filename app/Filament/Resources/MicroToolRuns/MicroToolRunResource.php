<?php

namespace App\Filament\Resources\MicroToolRuns;

use App\Filament\Resources\MicroToolRuns\Pages\ListMicroToolRuns;
use App\Filament\Resources\MicroToolRuns\Tables\MicroToolRunsTable;
use App\Models\MicroToolRun;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MicroToolRunResource extends Resource
{
    protected static ?string $model = MicroToolRun::class;

    protected static ?string $navigationLabel = 'Tool runs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlay;

    protected static string|\UnitEnum|null $navigationGroup = 'Tools';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),
                TextEntry::make('tool.slug')
                    ->label('Tool slug'),
                TextEntry::make('status'),
                TextEntry::make('request_ip'),
                TextEntry::make('guest_token')
                    ->copyable(),
                TextEntry::make('input_summary')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''),
                TextEntry::make('result_summary')
                    ->columnSpanFull(),
                TextEntry::make('result')
                    ->label('Result JSON')
                    ->formatStateUsing(function ($state): string {
                        if (is_object($state) && isset($state->payload) && is_array($state->payload)) {
                            return json_encode($state->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                        }

                        return '';
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return MicroToolRunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMicroToolRuns::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * @return Builder<MicroToolRun>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['tool', 'user', 'result']);
    }
}
