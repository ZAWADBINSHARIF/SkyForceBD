<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\ShipmentType;
use App\Enums\WorkProcess;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn($record) => $record->customer_phone),

                TextColumn::make('order_status')
                    ->label('Order Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('delivery_status')
                    ->label('Delivery')
                    ->badge(),        // color + icon come from the enum

                TextColumn::make('work_process')
                    ->label('Process')
                    ->badge(),

                TextColumn::make('shipment_type')
                    ->label('Shipment')
                    ->badge(),

                TextColumn::make('total_price')
                    ->label('Total (৳)')
                    ->money('BDT')
                    ->sortable(),

                TextColumn::make('advance_payment')
                    ->label('Advance (৳)')
                    ->money('BDT')
                    ->toggleable(),

                TextColumn::make('due_payment')
                    ->label('Due (৳)')
                    ->money('BDT')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->toggleable(),

                TextColumn::make('order_receive_date')
                    ->label('Received')
                    ->dateTime('d M Y')
                    ->timezone('Asia/Dhaka')
                    ->sortable(),

                TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->date('d M Y')
                    ->timezone('Asia/Dhaka')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->relationship('customer', 'full_name')
                    ->native(false),

                SelectFilter::make('order_status')
                    ->options(OrderStatus::class)
                    ->native(false),

                SelectFilter::make('delivery_status')
                    ->options(DeliveryStatus::class)
                    ->native(false),

                SelectFilter::make('work_process')
                    ->options(WorkProcess::class)
                    ->native(false),

                SelectFilter::make('shipment_type')
                    ->options(ShipmentType::class)
                    ->native(false),

                SelectFilter::make('order_call')
                    ->relationship('user', 'name')
                    ->native(false),

                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('order_place_date'),
                    ]),

            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_receive_date', 'desc')
            ->striped();
    }
}
