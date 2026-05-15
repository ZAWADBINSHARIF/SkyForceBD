<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Enums\FieldLength;
use App\Enums\StoragePath;
use App\Enums\TransactionStatus;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Reference')
                    ->description('Identifiers and order link.')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->columns(2)
                    ->schema([
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
                            ->required()
                            ->minValue(0)
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaction_number')
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('transaction_number')
                    ->label('TXN #')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('bank_transaction_id')
                    ->searchable()
                    ->copyable()
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'card'           => 'Card',
                        'mobile_banking' => 'Mobile Banking',
                        'bank_transfer'  => 'Bank Transfer',
                        'cod'            => 'COD',
                        default          => ucfirst($state ?? '—'),
                    })
                    ->color('info'),

                TextColumn::make('card_brand')
                    ->label('Brand')
                    ->placeholder('—'),

                TextColumn::make('payment_amount')
                    ->label('Amount (৳)')
                    ->money('BDT')
                    ->sortable(),

                TextColumn::make('store_amount')
                    ->label('Store (৳)')
                    ->money('BDT')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                ImageColumn::make('bank_transaction_image')
                    ->label('Proof')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl('https://placehold.co/40x40?text=N/A'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TransactionStatus::class),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
