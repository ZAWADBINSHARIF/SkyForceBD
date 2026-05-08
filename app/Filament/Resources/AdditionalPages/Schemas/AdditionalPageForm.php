<?php

namespace App\Filament\Resources\AdditionalPages\Schemas;

use App\Enums\FieldLength;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AdditionalPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page Identity')
                    ->description('Page title and URL slug.')
                    ->icon('heroicon-o-document')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Page Title')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            )
                            ->placeholder('e.g. Privacy Policy')
                            ->columnSpan(1),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. privacy-policy')
                            ->helperText('Auto-generated from title.')
                            ->columnSpan(1),
                    ]),

                Section::make('Page Content')
                    ->description('Rich text content for this page.')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Content')
                            ->nullable()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'h2',
                                'h3',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
