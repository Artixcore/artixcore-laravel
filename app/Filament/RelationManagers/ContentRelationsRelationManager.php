<?php

namespace App\Filament\RelationManagers;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\ContentRelation;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContentRelationsRelationManager extends RelationManager
{
    protected static string $relationship = 'contentRelationsAsSource';

    protected static bool $shouldSkipAuthorization = true;

    /**
     * @return array<string, string>
     */
    private static function relatedTypeOptions(): array
    {
        return [
            Article::class => 'Article',
            CaseStudy::class => 'Case study',
            PortfolioItem::class => 'Portfolio item',
            Service::class => 'Service',
            Product::class => 'SaaS platform (product)',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function relationTypeOptions(): array
    {
        return [
            ContentRelation::RELATED_ARTICLE => 'Related article',
            ContentRelation::RELATED_CASE_STUDY => 'Related case study',
            ContentRelation::RELATED_PORTFOLIO => 'Related portfolio item',
            ContentRelation::RELATED_SERVICE => 'Related service',
            ContentRelation::RELATED_PLATFORM => 'Related platform',
        ];
    }

    /**
     * @return array<int|string, string>
     */
    private static function optionsForRelatedType(?string $type): array
    {
        if ($type === null || $type === '' || ! class_exists($type)) {
            return [];
        }

        return match ($type) {
            Article::class => Article::query()->orderBy('title')->limit(500)->pluck('title', 'id')->all(),
            CaseStudy::class => CaseStudy::query()->orderBy('title')->limit(500)->pluck('title', 'id')->all(),
            PortfolioItem::class => PortfolioItem::query()->orderBy('title')->limit(500)->pluck('title', 'id')->all(),
            Service::class => Service::query()->orderBy('title')->limit(500)->pluck('title', 'id')->all(),
            Product::class => Product::query()->orderBy('title')->limit(500)->pluck('title', 'id')->all(),
            default => [],
        };
    }

    private static function relatedAdminLabel(ContentRelation $record): string
    {
        /** @var Model|null $rel */
        $rel = $record->relationLoaded('related') ? $record->related : null;
        if ($rel !== null) {
            return match (true) {
                isset($rel->title) && is_string($rel->title) && $rel->title !== '' => $rel->title,
                default => class_basename($rel).' #'.$rel->getKey(),
            };
        }

        return class_basename((string) $record->related_type).' #'.$record->related_id;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('relation_type')
                    ->label('Relation')
                    ->options(self::relationTypeOptions())
                    ->required(),
                Select::make('related_type')
                    ->label('Related type')
                    ->options(self::relatedTypeOptions())
                    ->live()
                    ->required(),
                Select::make('related_id')
                    ->label('Related record')
                    ->options(fn (Get $get): array => self::optionsForRelatedType($get('related_type')))
                    ->required()
                    ->searchable(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_featured')
                    ->label('Featured edge')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('related'))
            ->columns([
                TextColumn::make('relation_type')
                    ->label('Relation')
                    ->formatStateUsing(fn (?string $state): string => self::relationTypeOptions()[$state ?? ''] ?? (string) $state),
                TextColumn::make('related_type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => self::relatedTypeOptions()[$state ?? ''] ?? class_basename((string) $state)),
                TextColumn::make('related_id')
                    ->label('Related')
                    ->formatStateUsing(fn ($state, ContentRelation $record): string => self::relatedAdminLabel($record)),
                TextColumn::make('sort_order')->sortable(),
                IconColumn::make('is_featured')->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
