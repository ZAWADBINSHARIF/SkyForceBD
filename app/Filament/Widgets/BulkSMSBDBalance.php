<?php

namespace App\Filament\Widgets;

use App\Services\BulkSMSBDService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BulkSMSBDBalance extends StatsOverviewWidget
{

    use HasWidgetShield;

    protected function getStats(): array
    {

        $SMS = app(BulkSMSBDService::class);

        $result = $SMS->getBalance();

        $balance = '';

        if ($result['success']) {
            $balance = $result['data']['balance'];
        } else {
            $balance = 'Unable to get balance';
        }

        return [
            Stat::make('SMS Balance', (string) $balance . " TK")
                ->description(
                    $balance > 500
                        ? 'Sufficient SMS balance'
                        : 'Balance running low'
                )
                ->descriptionIcon(
                    $balance > 500
                        ? 'heroicon-m-check-badge'
                        : 'heroicon-m-exclamation-triangle'
                )
                ->color(
                    $balance > 500
                        ? 'success'
                        : 'warning'
                )
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
        ];
    }
}
