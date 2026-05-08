<?php

namespace App\Filament\Resources\Offices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OfficesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            ImageColumn::make('image_url')
                ->label('Photo')
                ->disk('public')
                ->square()
                ->defaultImageUrl('https://placehold.co/60x60?text=Office'),

            TextColumn::make('flag')
                ->label('')
                ->searchable(false),

            TextColumn::make('country')
                ->label('Country')
                ->searchable()
                ->sortable(),

            TextColumn::make('label')
                ->label('Label')
                ->badge()
                ->color('info'),

            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->weight('semibold'),

            TextColumn::make('phone')
                ->label('Phone')
                ->copyable()
                ->placeholder('—'),

            TextColumn::make('sort_order')
                ->label('Order')
                ->sortable()
                ->badge()
                ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->striped();
    }
}
