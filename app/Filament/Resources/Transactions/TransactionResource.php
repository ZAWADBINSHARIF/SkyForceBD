<?php

namespace App\Filament\Resources\Transactions;

use App\Enums\NavigationGroup;
use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon  = Heroicon::OutlinedCreditCard;
    protected static string|UnitEnum|null   $navigationGroup = NavigationGroup::Orders;
    protected static ?int                   $navigationSort  = 2;

    protected static ?string $recordTitleAttribute = 'transaction_number';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', TransactionStatus::Pending)->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['order.order_number', 'transaction_number', 'bank_transaction_id'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Order Number' => $record->order->order_number,
            'Customer Name' => $record->order->customer_name,
            'Customer Phone' => $record->order->customer_phone,
            'Order Status' => $record->order->order_status,
            'Delivery Status' => $record->order->delivery_status,
            'Transaction Status' => $record->status,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
