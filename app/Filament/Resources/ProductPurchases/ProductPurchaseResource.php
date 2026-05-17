<?php

namespace App\Filament\Resources\ProductPurchases;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ProductPurchases\Pages\CreateProductPurchase;
use App\Filament\Resources\ProductPurchases\Pages\EditProductPurchase;
use App\Filament\Resources\ProductPurchases\Pages\ListProductPurchases;
use App\Filament\Resources\ProductPurchases\Schemas\ProductPurchaseForm;
use App\Filament\Resources\ProductPurchases\Tables\ProductPurchasesTable;
use App\Models\ProductPurchase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProductPurchaseResource extends Resource
{
    protected static ?string $model = ProductPurchase::class;

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::OutlinedShoppingBag;
    protected static string|UnitEnum|null   $navigationGroup = NavigationGroup::Orders;
    protected static ?int                   $navigationSort  = 3;

    protected static ?string $recordTitleAttribute = 'Purchase Product';

    public static function getGloballySearchableAttributes(): array
    {
        return ['order.order_number', 'customer_name', 'phone_number', 'product_title'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Order Number' => $record->order->order_number,
            'Product Name' => $record->product_title,
            'Customer Name' => $record->customer_name,
            'Customer Phone' => $record->phone_number,
            'Purchase Product Status' => $record->status,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ProductPurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductPurchasesTable::configure($table);
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
            'index' => ListProductPurchases::route('/'),
            'create' => CreateProductPurchase::route('/create'),
            'edit' => EditProductPurchase::route('/{record}/edit'),
        ];
    }
}
