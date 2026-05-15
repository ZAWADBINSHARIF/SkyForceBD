<?php

namespace App\Enums;

use App\Models\Order;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor, HasIcon
{
    case OrderRequest = 'order_request';
    case Responsed    = 'responsed';
    case Accepted     = 'accepted';
    case Rejected     = 'rejected';

    public static function values():array {
        return array_column(self::cases(), 'value', 'name');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::OrderRequest => 'Order Request',
            self::Responsed    => 'Responsed',
            self::Accepted     => 'Accepted',
            self::Rejected     => 'Rejected',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OrderRequest => 'gray',
            self::Responsed    => 'warning',
            self::Accepted     => 'success',
            self::Rejected     => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::OrderRequest => 'heroicon-o-paper-airplane',
            self::Responsed    => 'heroicon-o-chat-bubble-left-ellipsis',
            self::Accepted     => 'heroicon-o-check-circle',
            self::Rejected     => 'heroicon-o-x-circle',
        };
    }
}
