<?php

namespace App\Filament\Resources\AiApprovals;

use App\Filament\Resources\AiApprovals\Pages\CreateAiApproval;
use App\Filament\Resources\AiApprovals\Pages\EditAiApproval;
use App\Filament\Resources\AiApprovals\Pages\ListAiApprovals;
use App\Filament\Resources\AiApprovals\Schemas\AiApprovalForm;
use App\Filament\Resources\AiApprovals\Tables\AiApprovalsTable;
use App\Models\AiApproval;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AiApprovalResource extends Resource
{
    protected static ?string $model = AiApproval::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static string|UnitEnum|null $navigationGroup = 'AI & automation';

    protected static ?int $navigationSort = 23;

    public static function form(Schema $schema): Schema
    {
        return AiApprovalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiApprovalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiApprovals::route('/'),
            'create' => CreateAiApproval::route('/create'),
            'edit' => EditAiApproval::route('/{record}/edit'),
        ];
    }
}
