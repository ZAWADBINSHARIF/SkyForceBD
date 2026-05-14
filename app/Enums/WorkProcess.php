<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WorkProcess: string implements HasLabel, HasColor, HasIcon
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Purchased  = 'purchased';
    case Shipped    = 'shipped';
    case Completed  = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending    => 'Pending',
            self::Processing => 'Processing',
            self::Purchased  => 'Purchased',
            self::Shipped    => 'Shipped',
            self::Completed  => 'Completed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending    => 'gray',
            self::Processing => 'warning',
            self::Purchased  => 'info',
            self::Shipped    => 'primary',
            self::Completed  => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending    => 'heroicon-o-clock',
            self::Processing => 'heroicon-o-arrow-path',
            self::Purchased  => 'heroicon-o-shopping-bag',
            self::Shipped    => 'heroicon-o-truck',
            self::Completed  => 'heroicon-o-check-badge',
        };
    }
}