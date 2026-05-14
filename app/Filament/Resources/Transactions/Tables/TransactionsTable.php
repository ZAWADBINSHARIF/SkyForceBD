<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('transaction_number')
                    ->label('TXN #')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('bank_transaction_id')
                    ->searchable()
                    ->copyable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'card'           => 'Card',
                        'mobile_banking' => 'Mobile Banking',
                        'bank_transfer'  => 'Bank Transfer',
                        'cod'            => 'COD',
                        default          => ucfirst($state ?? '—'),
                    })
                    ->color('info'),

                TextColumn::make('card_brand')
                    ->label('Brand')
                    ->placeholder('—'),

                TextColumn::make('payment_amount')
                    ->label('Amount (৳)')
                    ->money('BDT')
                    ->sortable(),

                TextColumn::make('store_amount')
                    ->label('Store (৳)')
                    ->money('BDT')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                ImageColumn::make('bank_transaction_image')
                    ->label('Proof')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl('https://placehold.co/40x40?text=N/A'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'success'  => 'Success',
                        'failed'   => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'card'           => 'Card',
                        'mobile_banking' => 'Mobile Banking',
                        'bank_transfer'  => 'Bank Transfer',
                        'cod'            => 'COD',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
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
