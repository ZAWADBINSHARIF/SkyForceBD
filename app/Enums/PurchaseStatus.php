<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PurchaseStatus: string implements HasLabel, HasColor, HasIcon
{
    case Pending   = 'pending';
    case Purchased = 'purchased';
    case Shipped   = 'shipped';
    case Arrived   = 'arrived';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending   => 'Pending',
            self::Purchased => 'Purchased',
            self::Shipped   => 'Shipped',
            self::Arrived   => 'Arrived',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending   => 'gray',
            self::Purchased => 'info',
            self::Shipped   => 'warning',
            self::Arrived   => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending   => 'heroicon-o-clock',
            self::Purchased => 'heroicon-o-shopping-bag',
            self::Shipped   => 'heroicon-o-truck',
            self::Arrived   => 'heroicon-o-inbox-arrow-down',
            self::Delivered => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }
}
