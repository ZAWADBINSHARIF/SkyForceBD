<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\DeliveryStatus;
use App\Enums\FieldLength;
use App\Enums\OrderStatus;
use App\Enums\ShipmentType;
use App\Enums\WorkProcess;
use App\Models\Customer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Order Identity ────────────────────────────────────
                Section::make('Order Identity')
                    ->description('Order number and key dates.')
                    ->icon('heroicon-o-document-text')
                    ->columns(3)
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('#SKY-dmy-EWRE8990932')
                            ->columnSpan(1)
                            ->hiddenOn(Operation::Create)
                            ->disabled(),

                        DateTimePicker::make('order_receive_date')
                            ->label('Received At')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        DateTimePicker::make('order_place_date')
                            ->label('Order Placed At')
                            ->nullable()
                            ->helperText('Set when advance payment is confirmed.')
                            ->columnSpan(1),
                    ])->columnSpanFull(),

                // ── Status & Logistics ────────────────────────────────
                Section::make('Status & Logistics')
                    ->description('Delivery progress and shipment information.')
                    ->icon('heroicon-o-truck')
                    ->columns(3)
                    ->schema([
                        Select::make('order_status')
                            ->label('Order Status')
                            ->required()
                            ->options(OrderStatus::class)
                            ->default(OrderStatus::OrderRequest)
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('delivery_status')
                            ->label('Delivery Status')
                            ->required()
                            ->options(DeliveryStatus::class)
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('work_process')
                            ->label('Work Process')
                            ->required()
                            ->options(WorkProcess::class)
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('shipment_type')
                            ->label('Shipment Type')
                            ->options(ShipmentType::class)
                            ->nullable()
                            ->native(false)
                            ->columnSpan(1),

                        DatePicker::make('delivery_date')
                            ->label('Expected Delivery Date')
                            ->nullable()
                            ->columnSpan(1),

                        Select::make('order_call')
                            ->label('Order Call')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('purchase_product_link')
                            ->label('Purchase Product Link')
                            ->url()
                            ->maxLength(FieldLength::Long->value)
                            ->placeholder('https://...')
                            ->columnSpan(1),
                    ])->columnSpanFull(),

                // ── Products ──────────────────────────────────────────
                Section::make('Products')
                    ->description('Items in this order. Total price auto-calculates.')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Repeater::make('products')
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Product Name')
                                    ->maxLength(FieldLength::Default->value)
                                    ->columnSpan(2),

                                TextInput::make('link')
                                    ->label('Product Link')
                                    ->url()
                                    ->required()
                                    ->maxLength(FieldLength::Long->value)
                                    ->placeholder('https://...')
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $set('total_price', (float) $state * (float) $get('unit_price'));
                                    })
                                    ->columnSpan(1),

                                TextInput::make('unit_price')
                                    ->label('Unit Price (৳)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $set('total_price', (float) $get('quantity') * (float) $state);
                                    })
                                    ->columnSpan(1),

                                TextInput::make('total_price')
                                    ->label('Total (৳)')
                                    ->numeric()
                                    ->readOnly()
                                    ->columnSpan(1),
                            ])
                            ->columns(7)
                            ->addActionLabel('+ Add Product')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn(array $state) => $state['name'] ?? 'New Product')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                // ── Customer Details ──────────────────────────────────
                Section::make('Customer Details')
                    ->description('Who placed this order. Filled from customer or entered manually.')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Linked Customer')
                            ->relationship('customer', 'full_name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $customer = Customer::find($state);
                                    $set('customer_name', $customer?->full_name);
                                    $set('customer_phone', $customer?->phone_number);
                                    $set('customer_address', $customer?->address);
                                }
                            })
                            ->columnSpanFull(),

                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('Auto-filled or enter manually')
                            ->columnSpan(1),

                        TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->required()
                            ->tel()
                            ->maxLength(FieldLength::Short->value)
                            ->placeholder('+8801XXXXXXXXX')
                            ->columnSpan(1),

                        Textarea::make('customer_address')
                            ->label('Customer Address')
                            ->maxLength(FieldLength::ExtraLong->value)
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('customer_remark')
                            ->label('Customer Remark')
                            ->maxLength(FieldLength::ExtraLong->value)
                            ->rows(2)
                            ->placeholder('Notes from the customer…')
                            ->columnSpan(1),

                        Textarea::make('employee_remark')
                            ->label('Employee Remark')
                            ->maxLength(FieldLength::ExtraLong->value)
                            ->rows(2)
                            ->placeholder('Internal staff notes…')
                            ->columnSpan(1),
                    ]),

                // ── Financials ────────────────────────────────────────
                Section::make('Financial Summary')
                    ->description('Pricing, payment, and outstanding balance.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('total_price')
                            ->label('Total Price (৳)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->columnSpan(1),

                        TextInput::make('shipping_charge')
                            ->label('Shipping Charge (৳)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->default(0)
                            ->columnSpan(1),

                        TextInput::make('product_weight')
                            ->label('Product Weight (kg)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->columnSpan(1),

                        TextInput::make('advance_payment')
                            ->label('Advance Payment (৳)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('due_payment')
                            ->label('Due Payment (৳)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->default(0)
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
