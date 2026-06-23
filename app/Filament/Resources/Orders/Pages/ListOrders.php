<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Livewire\OrderPaidSummaryWidget;
use App\Models\Order;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Order_request' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('order_status', OrderStatus::OrderRequest))
                ->badge(Order::query()->where('order_status', OrderStatus::OrderRequest)->count()),
            'Accepted' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('order_status', OrderStatus::Accepted))
                ->badge(Order::query()->where('order_status', OrderStatus::Accepted)->count()),
            'Rejected' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('order_status', OrderStatus::Rejected))
                ->badge(Order::query()->where('order_status', OrderStatus::Rejected)->count())
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'Order_request';
    }
}
