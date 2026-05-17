<?php

namespace App\Filament\Resources\ProductPurchases\Tables;

use App\Enums\PurchaseStatus;
use App\Models\ProductPurchase;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductPurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('order.order_number')
                ->label('Order #')
                ->searchable()
                ->sortable()
                ->weight('semibold')
                ->copyable(),

            TextColumn::make('product_title')
                ->label('Product')
                ->searchable()
                ->limit(30)
                ->tooltip(fn($record) => $record->product_title),

            TextColumn::make('customer_name')
                ->label('Customer')
                ->searchable()
                ->description(fn($record) => $record->phone_number),

            TextColumn::make('ecommerce_platform')
                ->label('Platform')
                ->badge()
                ->color('info')
                ->placeholder('—'),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable(),

            TextColumn::make('logistics_company')
                ->label('Logistics')
                ->placeholder('—')
                ->toggleable(),

            TextColumn::make('logistics_tracking')
                ->label('Tracking')
                ->copyable()
                ->placeholder('—')
                ->toggleable(),

            TextColumn::make('courier_entry')
                ->label('Courier Entry')
                ->date('d M Y')
                ->sortable()
                ->toggleable(),

            TextColumn::make('product_buy_amount')
                ->label('Cust. Pays (৳)')
                ->money('BDT')
                ->sortable()
                ->summarize(Sum::make()->label('Total Customer Pays')->money('BDT')),

            TextColumn::make('product_purchase_price')
                ->label('Cost (৳)')
                ->money('BDT')
                ->sortable()
                ->toggleable()
                ->summarize(Sum::make()->label('Total Product Purchase')->money('BDT')),

            TextColumn::make('shipping_and_extra_cost')
                ->label('Shipping (৳)')
                ->money('BDT')
                ->toggleable()
                ->summarize(Sum::make()->label('Total Extra cost')->money('BDT')),

            TextColumn::make('profit')
                ->label('Profit (৳)')
                ->money('BDT')
                ->sortable()
                ->color(fn($state) => (float) $state >= 0 ? 'success' : 'danger')
                ->weight('semibold')
                ->summarize(Sum::make()->label('Total Profit')->money('BDT')),

            TextColumn::make('account_name')
                ->label('Account')
                ->badge()
                ->color('gray')
                ->placeholder('—')
                ->toggleable(),

            TextColumn::make('pay_done_by')
                ->label('Paid By')
                ->placeholder('—')
                ->toggleable(),

            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
            SelectFilter::make('status')
                ->options(PurchaseStatus::class)
                ->native(false),

            SelectFilter::make('ecommerce_platform')
                ->options(
                    fn() => ProductPurchase::query()
                        ->whereNotNull('ecommerce_platform')
                        ->distinct()
                        ->pluck('ecommerce_platform', 'ecommerce_platform')
                )
                ->native(false),

            SelectFilter::make('account_name')
                ->options(
                    fn() => ProductPurchase::query()
                        ->whereNotNull('account_name')
                        ->distinct()
                        ->pluck('account_name', 'account_name')
                )
                ->native(false),
        ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
