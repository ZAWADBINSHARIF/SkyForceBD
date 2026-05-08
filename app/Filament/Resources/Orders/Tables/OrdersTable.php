<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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

                TextColumn::make('delivery_status')
                    ->label('Delivery')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending'    => 'gray',
                        'processing' => 'warning',
                        'shipped'    => 'info',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                    }),

                TextColumn::make('work_process')
                    ->label('Process')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending'    => 'gray',
                        'processing' => 'warning',
                        'purchased'  => 'info',
                        'shipped'    => 'primary',
                        'completed'  => 'success',
                    }),

                TextColumn::make('shipment_type')
                    ->label('Shipment')
                    ->badge()
                    ->color('gray'),

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
                    ->sortable(),

                TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->date('d M Y')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('delivery_status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),

                SelectFilter::make('work_process')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'purchased'  => 'Purchased',
                        'shipped'    => 'Shipped',
                        'completed'  => 'Completed',
                    ]),

                SelectFilter::make('shipment_type')
                    ->options([
                        'air'  => 'Air',
                        'sea'  => 'Sea',
                        'road' => 'Road',
                    ]),
            ])
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
