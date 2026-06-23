<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalTransactionAmount extends StatsOverviewWidget
{

    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected int|array|null $columns = 1;

    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $totalAmount = Transaction::query()
            ->where('status', TransactionStatus::Success)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('payment_amount');

        return [
            Stat::make('Total Transaction Amount for This Month', '৳' . number_format($totalAmount, 2))
                ->description('Successful transactions only')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
