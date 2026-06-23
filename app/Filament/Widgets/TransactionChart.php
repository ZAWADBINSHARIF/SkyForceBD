<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransactionChart extends ChartWidget
{

    use HasWidgetShield;

    protected ?string $heading = 'Transaction Chart';

    protected static ?int $sort = 3;

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

        $transactions = Transaction::query()
            ->whereYear('created_at', $year)
            ->where('status', TransactionStatus::Success)
            ->get()
            ->groupBy(fn(Transaction $transaction) => $transaction->created_at->month);

        $months = $this->filter !== 'all'
            ? [(int) $this->filter]
            : range(1, 12);

        $labels = [];
        $countData = [];
        $amountData = [];

        foreach ($months as $month) {
            $labels[] = Carbon::create()->month($month)->format('M');

            $monthTransactions = $transactions->get($month, collect());

            $countData[] = $monthTransactions->count();
            $amountData[] = $monthTransactions->sum('payment_amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transaction Count',
                    'data' => $countData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Amount',
                    'data' => $amountData,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => '#22c55e',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Count',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Amount',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
