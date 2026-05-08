<?php

namespace App\Filament\Resources\AdditionalPages;

use App\Enums\NavigationGroup;
use App\Filament\Resources\AdditionalPages\Pages\CreateAdditionalPage;
use App\Filament\Resources\AdditionalPages\Pages\EditAdditionalPage;
use App\Filament\Resources\AdditionalPages\Pages\ListAdditionalPages;
use App\Filament\Resources\AdditionalPages\Schemas\AdditionalPageForm;
use App\Filament\Resources\AdditionalPages\Tables\AdditionalPagesTable;
use App\Models\AdditionalPage as ModelAdditionalPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdditionalPageResource extends Resource
{
    protected static ?string $model = ModelAdditionalPage::class;

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::OutlinedDocumentDuplicate;
    protected static string|UnitEnum|null   $navigationGroup = NavigationGroup::Website;
    protected static ?int                   $navigationSort  = 3;

    protected static ?string $recordTitleAttribute = 'AdditionalPage';

    public static function form(Schema $schema): Schema
    {
        return AdditionalPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdditionalPagesTable::configure($table);
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
            'index' => ListAdditionalPages::route('/'),
            'create' => CreateAdditionalPage::route('/create'),
            'edit' => EditAdditionalPage::route('/{record}/edit'),
        ];
    }
}
