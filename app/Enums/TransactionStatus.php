<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionStatus: string implements HasLabel, HasColor, HasIcon
{
    case Success  = 'success';
    case Failed   = 'failed';
    case Canceled = 'canceled';
    case Pending  = 'pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::Success  => 'Success',
            self::Failed   => 'Failed',
            self::Canceled => 'Canceled',
            self::Pending => 'Pending',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Success  => 'success',
            self::Failed   => 'danger',
            self::Canceled => 'gray',
            self::Pending => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Success  => 'heroicon-o-check-circle',
            self::Failed   => 'heroicon-o-x-circle',
            self::Canceled => 'heroicon-o-no-symbol',
            self::Pending => 'heroicon-o-no-symbol',
        };
    }
}
