<?php

namespace App\Filament\Resources\Offices\Schemas;

use App\Enums\FieldLength;
use App\Enums\StoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OfficeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Office Identity')
                    ->description('Country, label and display name.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->columns(3)
                    ->schema([
                        TextInput::make('flag')
                            ->label('Flag Emoji')
                            ->required()
                            ->maxLength(FieldLength::Tiny->value)
                            ->placeholder('🇧🇩')
                            ->columnSpan(1),

                        TextInput::make('country')
                            ->label('Country')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('Bangladesh')
                            ->columnSpan(1),

                        TextInput::make('label')
                            ->label('Office Label')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('Head Office')
                            ->columnSpan(1),

                        TextInput::make('name')
                            ->label('Office Display Name')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('Sky Force BD HQ')
                            ->columnSpanFull(),
                    ]),

                Section::make('Office Photo')
                    ->description('Upload an office image (max 4 MB).')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Office Image')
                            ->image()
                            ->disk('public')
                            ->directory(StoragePath::OfficeImage->value)
                            ->maxSize(4096)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('Location & Contact')
                    ->description('Physical address and communication details.')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->label('Address')
                            ->required()
                            ->maxLength(FieldLength::ExtraLong->value)
                            ->placeholder('New Elephant Road, Dhaka 1205')
                            ->columnSpanFull(),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('+880 1700-000000')
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('bd@skyforcebd.com')
                            ->columnSpan(1),

                        TextInput::make('hours')
                            ->label('Working Hours')
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('Sat – Thu: 9:00 AM – 6:00 PM')
                            ->columnSpan(1),

                        TextInput::make('closed')
                            ->label('Closed On')
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('Friday: Closed')
                            ->columnSpan(1),

                        TextInput::make('maps_url')
                            ->label('Google Maps URL')
                            ->url()
                            ->nullable()
                            ->maxLength(FieldLength::Long->value)
                            ->placeholder('https://maps.google.com/?q=...')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
