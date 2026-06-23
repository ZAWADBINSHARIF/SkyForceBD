<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChart extends ChartWidget
{
    use HasWidgetShield;

    protected ?string $heading = 'Order Chart';

    protected static ?int $sort = 2;

    public ?string $filter = 'all';

    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'All months',
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    protected function getData(): array
    {
        $year = now()->year;

        $orders = Order::query()
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn(Order $order) => $order->created_at->month);

        $months = $this->filter !== 'all'
            ? [(int) $this->filter]
            : range(1, 12);

        $labels = [];
        $requestData = [];
        $acceptedData = [];
        $rejectedData = [];

        foreach ($months as $month) {
            $labels[] = Carbon::create()->month($month)->format('M');

            $monthOrders = $orders->get($month, collect());

            $requestData[] = $monthOrders->where('order_status', OrderStatus::OrderRequest)->count();
            $acceptedData[] = $monthOrders->where('order_status', OrderStatus::Accepted)->count();
            $rejectedData[] = $monthOrders->where('order_status', OrderStatus::Rejected)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Request',
                    'data' => $requestData,
                    'backgroundColor' => '#FACC15', // Yellow 400
                    'borderColor' => '#EAB308',     // Yellow 500
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Accepted',
                    'data' => $acceptedData,
                    'backgroundColor' => '#4ADE80', // Green 400
                    'borderColor' => '#22C55E',     // Green 500
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Rejected',
                    'data' => $rejectedData,
                    'backgroundColor' => '#F87171', // Red 400
                    'borderColor' => '#EF4444',     // Red 500
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
