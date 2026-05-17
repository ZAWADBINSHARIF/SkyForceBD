<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Enums\FieldLength;
use App\Enums\StoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Section::make('Banner Details')
                ->description('Upload a banner image and optionally link it somewhere.')
                ->icon('heroicon-o-rectangle-stack')
                ->columns(2)
                ->schema([
                    FileUpload::make('image')
                        ->label('Banner Image')
                        ->image()
                        ->disk('public')
                        ->directory(StoragePath::BannerImage->value)
                        ->maxSize(4096)
                        ->imageEditor()
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('link')
                        ->label('Link URL')
                        ->url()
                        ->nullable()
                        ->maxLength(FieldLength::Long->value)
                        ->placeholder('https://...')
                        ->helperText('Where should this banner redirect?')
                        ->columnSpan(1),

                    TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Lower number = shown first.')
                        ->columnSpan(1),
                ]),
            ]);
    }
}
