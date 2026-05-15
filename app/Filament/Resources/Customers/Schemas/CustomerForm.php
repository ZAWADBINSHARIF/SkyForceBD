<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\FieldLength;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Basic customer identity details.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. Rahim Uddin')
                            ->columnSpan(1),

                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->required()
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('+8801XXXXXXXXX')
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpan(1),

                        TextInput::make('address')
                            ->label('Address')
                            ->maxLength(FieldLength::ExtraLong->value)
                            ->placeholder('House, Road, Area, City')
                            ->columnSpanFull(),
                    ]),

                Section::make('Security')
                    ->description('Set or update the customer password.')
                    ->icon('heroicon-o-lock-closed')
                    ->columns(2)
                    ->schema([
                        TextInput::make('password_hash')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => bcrypt($state))
                            ->required(fn(string $operation) => $operation === 'create')
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpanFull(),
                    ]),

                Section::make('Avatar')
                    // ->description('Upload a profile picture (max 4 MB).')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        // FileUpload::make('avatar_url')
                        //     ->label('Profile Picture')
                        //     ->image()
                        //     ->disk('public')
                        //     ->directory(StoragePath::CustomerAvatar->value)
                        //     ->imageEditor()
                        //     ->circleCropper()
                        //     ->maxSize(4096)
                        //     ->columnSpanFull(),
                        TextEntry::make('avatar_url')
                            ->formatStateUsing(fn(string $state) => new HtmlString(
                                '<img src="' . asset('storage/' . $state) . '" class="w-10 h-10 rounded-full object-cover" />'
                            ))
                            ->html()
                    ]),
            ]);
    }
}
