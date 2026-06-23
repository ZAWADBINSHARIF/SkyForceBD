<?php

namespace App\Filament\Widgets;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\ShipmentType;
use App\Enums\WorkProcess;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OrderTable extends TableWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Order::query())
            ->recordUrl(fn($record) => OrderResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->description(fn($record) => $record->customer_phone),

                TextColumn::make('order_status')
                    ->label('Order Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('delivery_status')
                    ->label('Delivery')
                    ->badge(),        // color + icon come from the enum

                TextColumn::make('work_process')
                    ->label('Process')
                    ->badge(),

                TextColumn::make('shipment_type')
                    ->label('Shipment')
                    ->badge(),

                TextColumn::make('total_price')
                    ->label('Total (৳)')
                    ->money('BDT')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label('Revenue')
                            ->money('BDT')
                    ),

                TextColumn::make('advance_payment')
                    ->label('Advance (৳)')
                    ->money('BDT')
                    ->toggleable(),

                TextColumn::make('due_payment')
                    ->label('Due (৳)')
                    ->money('BDT')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->toggleable()
                    ->summarize(
                        Sum::make()
                            ->label('Total Due')
                            ->money('BDT')
                    ),

                TextColumn::make('total_paid')
                    ->label('Paid (৳)')
                    ->money('BDT')
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->label('Total Paid')
                            ->money('BDT')
                    ),

                TextColumn::make('order_receive_date')
                    ->label('Received')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('delivery_date')
                    ->label('Delivery Date')
                    ->date('d M Y')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->relationship('customer', 'full_name')
                    ->native(false),

                SelectFilter::make('order_status')
                    ->options(OrderStatus::class)
                    ->native(false),

                SelectFilter::make('delivery_status')
                    ->options(DeliveryStatus::class)
                    ->native(false),

                SelectFilter::make('work_process')
                    ->options(WorkProcess::class)
                    ->native(false),

                SelectFilter::make('shipment_type')
                    ->options(ShipmentType::class)
                    ->native(false),

                SelectFilter::make('order_call')
                    ->relationship('user', 'name')
                    ->native(false),

                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('order_place_date')
                            ->label("Order Place Date"),
                        DateConstraint::make('order_receive_date')
                            ->label("Order Receive Date"),
                    ]),

            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn($record) => OrderResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_receive_date', 'desc')
            ->striped();
    }
}
