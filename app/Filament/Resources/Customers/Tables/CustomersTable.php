<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=Customer&background=random'),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone'),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->address),

                TextColumn::make('authProviders')
                    ->label('Auth Providers')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->authProviders->pluck('provider')->map(fn($p) => ucfirst($p))->toArray())
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
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
