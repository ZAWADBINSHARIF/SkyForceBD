<?php

namespace App\Filament\Resources\ProductPurchases\Schemas;

use App\Enums\FieldLength;
use App\Enums\PurchaseStatus;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProductPurchaseForm
{


    // ── Profit calculator ─────────────────────────────────────────

    private static function recalculateProfit(Get $get, Set $set): void
    {
        $buy      = (float) ($get('product_buy_amount')      ?? 0);
        $cost     = (float) ($get('product_purchase_price')  ?? 0);
        $shipping = (float) ($get('shipping_and_extra_cost') ?? 0);

        $set('profit', round($buy - $cost - $shipping, 2));
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Order & Product Selection ─────────────────────────
                Section::make('Order & Product')
                    ->description('Select the order then pick a product from it.')
                    ->icon(Heroicon::OutlinedShoppingBag)
                    ->columns(2)
                    ->schema([
                        Select::make('order_id')
                            ->label('Order')
                            ->options(
                                fn() => Order::query()
                                    ->whereNotNull('order_number')
                                    ->pluck('order_number', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (! $state) return;

                                $order = Order::find($state);
                                if (! $order) return;

                                // Auto-fill customer fields from order
                                $set('customer_name', $order->customer_name);
                                $set('phone_number',  $order->customer_phone);

                                // Reset product selection
                                $set('_selected_product_index', null);
                                $set('product_title',           null);
                                $set('customer_product_link',   null);
                                $set('product_buy_amount',      null);
                            })
                            ->columnSpanFull(),

                        Select::make('_selected_product_index')
                            ->label('Select Product from Order')
                            ->options(function (Get $get) {
                                $orderId = $get('order_id');
                                if (! $orderId) return [];

                                $order = Order::find($orderId);
                                if (! $order || empty($order->products)) return [];

                                return collect($order->products)
                                    ->mapWithKeys(fn($product, $index) => [
                                        $index => ($product['name'] ?? 'Product ' . ($index + 1))
                                            . ' — ৳' . number_format((float) ($product['total_price'] ?? 0)),
                                    ])
                                    ->toArray();
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $orderId = $get('order_id');
                                if ($state === null || ! $orderId) return;

                                $order = Order::find($orderId);
                                if (! $order || empty($order->products)) return;

                                $product = $order->products[$state] ?? null;
                                if (! $product) return;

                                $set('product_title',         $product['name']        ?? null);
                                $set('customer_product_link', $product['link']        ?? null);
                                $set('product_buy_amount',    $product['total_price'] ?? null);
                            })
                            ->placeholder('— choose order first —')
                            ->columnSpanFull(),

                        TextInput::make('product_title')
                            ->label('Product Title')
                            ->required()
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpan(1),

                        TextInput::make('customer_product_link')
                            ->label('Customer Product Link')
                            ->url()
                            ->nullable()
                            ->maxLength(FieldLength::Long->value)
                            ->columnSpan(1),
                    ]),

                // ── Customer Snapshot ─────────────────────────────────
                Section::make('Customer')
                    ->description('Auto-filled from the selected order.')
                    ->icon(Heroicon::OutlinedUser)
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpan(1),

                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->nullable()
                            ->maxLength(FieldLength::Short->value)
                            ->columnSpan(1),
                    ]),

                // ── Purchase Details ──────────────────────────────────
                Section::make('Purchase Details')
                    ->description('Who bought it, from where, and which account.')
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->columns(2)
                    ->schema([
                        TextInput::make('pay_done_by')
                            ->label('Pay Done By')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. Karim')
                            ->columnSpan(1),

                        TextInput::make('ecommerce_platform')
                            ->label('E-Commerce Platform')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. Taobao, 1688, Amazon')
                            ->columnSpan(1),

                        TextInput::make('receiver')
                            ->label('Receiver')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. Yiwu Office')
                            ->columnSpan(1),

                        TextInput::make('account_name')
                            ->label('Account Name')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->helperText('Which account was used for this purchase.')
                            ->columnSpan(1),
                    ]),

                // ── Logistics ─────────────────────────────────────────
                Section::make('Logistics')
                    ->description('Shipping and tracking information.')
                    ->icon(Heroicon::OutlinedTruck)
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(PurchaseStatus::class)
                            ->default(PurchaseStatus::Pending)
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('logistics_company')
                            ->label('Logistics Company')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->placeholder('e.g. DHL, YDH')
                            ->columnSpan(1),

                        TextInput::make('logistics_tracking')
                            ->label('Tracking Number')
                            ->nullable()
                            ->maxLength(FieldLength::Default->value)
                            ->columnSpan(1),

                        TextInput::make('information_link')
                            ->label('Tracking / Info Link')
                            ->url()
                            ->nullable()
                            ->maxLength(FieldLength::Long->value)
                            ->columnSpan(1),

                        TextInput::make('courier_entry')
                            ->label('Courier Entry Date')
                            ->nullable()
                            ->columnSpan(1),
                    ]),

                // ── Financials ────────────────────────────────────────
                Section::make('Financials')
                    ->description('Profit auto-calculates: buy amount − purchase price − shipping.')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columns(4)
                    ->schema([
                        TextInput::make('product_buy_amount')
                            ->label('Customer Pays (৳)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(Get $get, Set $set) =>
                                self::recalculateProfit($get, $set)
                            )
                            ->columnSpan(1),

                        TextInput::make('product_purchase_price')
                            ->label('Purchase Cost (৳)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(Get $get, Set $set) =>
                                self::recalculateProfit($get, $set)
                            )
                            ->columnSpan(1),

                        TextInput::make('shipping_and_extra_cost')
                            ->label('Shipping & Extra (৳)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(Get $get, Set $set) =>
                                self::recalculateProfit($get, $set)
                            )
                            ->columnSpan(1),

                        TextInput::make('profit')
                            ->label('Profit (৳)')
                            ->numeric()
                            ->readOnly()
                            ->columnSpan(1),
                    ])->columnSpanFull(),
            ]);
    }
}
