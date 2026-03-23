<?php

namespace App\Filament\Resources\MicroToolCategories;

use App\Filament\Resources\MicroToolCategories\Pages\CreateMicroToolCategory;
use App\Filament\Resources\MicroToolCategories\Pages\EditMicroToolCategory;
use App\Filament\Resources\MicroToolCategories\Pages\ListMicroToolCategories;
use App\Filament\Resources\MicroToolCategories\Schemas\MicroToolCategoryForm;
use App\Filament\Resources\MicroToolCategories\Tables\MicroToolCategoriesTable;
use App\Models\MicroToolCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MicroToolCategoryResource extends Resource
{
    protected static ?string $model = MicroToolCategory::class;

    protected static ?string $navigationLabel = 'Tool categories';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|\UnitEnum|null $navigationGroup = 'Tools';

    public static function form(Schema $schema): Schema
    {
        return MicroToolCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MicroToolCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMicroToolCategories::route('/'),
            'create' => CreateMicroToolCategory::route('/create'),
            'edit' => EditMicroToolCategory::route('/{record}/edit'),
        ];
    }
}
