<?php

namespace App\Filament\Resources\AdditionalPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdditionalPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')
                ->label('Page Title')
                ->searchable()
                ->sortable()
                ->weight('semibold'),

            TextColumn::make('slug')
                ->label('Slug')
                ->copyable()
                ->color('gray')
                ->prefix('/'),

            TextColumn::make('updated_at')
                ->label('Last Updated')
                ->dateTime('d M Y, h:i A')
                ->timezone('Asia/Dhaka')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Created')
                ->date('d M Y')
                ->timezone('Asia/Dhaka')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
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
            ]);
    }
}
