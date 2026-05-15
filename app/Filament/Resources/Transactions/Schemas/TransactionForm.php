<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\FieldLength;
use App\Enums\StoragePath;
use App\Enums\TransactionStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Reference')
                    ->description('Identifiers and order link.')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->columns(2)
                    ->schema([
                        Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('transaction_number')
                            ->label('Transaction Number')
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('TXN-XXXXXXXX')
                            ->readonly()
                            ->columnSpan(1),

                        TextInput::make('validation_id')
                            ->label('Validation ID')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->readOnly()
                            ->columnSpan(1),

                        TextInput::make('bank_transaction_id')
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options(TransactionStatus::class)
                            ->default('pending')
                            ->native(false)
                            ->columnSpan(1),
                    ]),

                Section::make('Proof of Payment')
                    ->description('Upload bank screenshot or payment proof (max 4 MB).')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('bank_transaction_image')
                            ->label('Bank / Transaction Screenshot')
                            ->image()
                            ->disk('public')
                            ->directory(StoragePath::TransactionProof->value)
                            ->maxSize(4096)
                            ->imageEditor()
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Payment Details')
                    ->description('How the payment was made.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->datalist([
                                'Card',
                                'Mobile Banking (bKash/Nagad)',
                                'Bank Transfer',
                                'Cash on Delivery',
                            ])
                            ->nullable(),

                        TextInput::make('card_brand')
                            ->label('Card / Wallet Brand')
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('Visa / bKash / Nagad…')
                            ->columnSpan(1),

                        TextInput::make('card_issuer_country')
                            ->label('Issuer Country')
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('Bangladesh')
                            ->columnSpan(1),

                        TextInput::make('account_holder_name')
                            ->label('Account Holder Name')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpan(1),

                        TextInput::make('payment_amount')
                            ->label('Payment Amount (৳)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('store_amount')
                            ->label('Store Amount (৳)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->helperText('Amount after gateway fees.')
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),

                Section::make("Raw Information")
                    ->schema([
                        KeyValueEntry::make('payment_info')
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
            ]);
    }
}
