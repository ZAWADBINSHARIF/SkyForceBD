<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\FieldLength;
use App\Enums\StoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;


class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Left column (main content) ────────────────────────
                Section::make('Product Information')
                    ->description('Core product details visible to customers.')
                    ->icon('heroicon-o-cube')
                    ->columnSpan(['lg' => 2])
                    ->columns(2)
                    ->schema([
                        TextInput::make('product_name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            )
                            ->placeholder('e.g. DJI Mini 4 Pro')
                            ->columnSpan(1),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. dji-mini-4-pro')
                            ->helperText('Auto-generated. Editable.')
                            ->columnSpan(1),

                        RichEditor::make('product_description')
                            ->label('Description')
                            ->maxLength(FieldLength::ExtraLong->value)
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'h2',
                                'h3',
                            ])
                            ->columnSpanFull(),
                    ]),

                // ── Right column (meta) ───────────────────────────────
                Section::make('Organisation')
                    ->description('Categorise and publish.')
                    ->icon('heroicon-o-folder')
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->required()->maxLength(FieldLength::Default->value),
                                TextInput::make('slug')->required()->unique(),
                            ]),

                        Toggle::make('published')
                            ->label('Published')
                            ->helperText('Toggle to make visible on the website.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                // ── Full-width images ─────────────────────────────────
                Section::make('Product Images')
                    ->description('Upload up to 10 images. Each must be under 4 MB. The first image is the cover.')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('product_images')
                            ->label('Images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->maxSize(4096)
                            ->disk('public')
                            ->directory(StoragePath::ProductImage->value)
                            ->imageEditor()
                            ->panelLayout('grid')
                            ->helperText('Drag to reorder. First image = cover photo.')
                            ->columnSpanFull(),
                    ]),
            ])->columns(3);
    }
}
