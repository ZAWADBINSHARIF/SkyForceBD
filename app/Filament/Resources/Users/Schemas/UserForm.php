<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\FieldLength;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff Details')
                    ->description('Admin panel user information.')
                    ->icon('heroicon-o-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. Karim Admin')
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(FieldLength::Short->value)
                            ->columnSpan(1),

                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('+8801XXXXXXXXX')
                            ->columnSpan(1),

                        Select::make('role')
                            ->label('Role')
                            ->required()
                            ->options([
                                'admin'   => 'Admin',
                                'manager' => 'Manager',
                                'staff'   => 'Staff',
                            ])
                            ->native(false)
                            ->columnSpan(1),
                    ]),

                Section::make('Security')
                    ->description('Set or update the login password.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => bcrypt($state))
                            ->required(fn(string $operation) => $operation === 'create')
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
