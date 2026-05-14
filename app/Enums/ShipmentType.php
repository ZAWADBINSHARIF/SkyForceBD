<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ShipmentType: string implements HasLabel, HasColor, HasIcon
{
    case Air  = 'air';
    case Sea  = 'sea';
    case Road = 'road';

    public function getLabel(): string
    {
        return match ($this) {
            self::Air  => 'Air',
            self::Sea  => 'Sea',
            self::Road => 'Road',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Air  => 'info',
            self::Sea  => 'primary',
            self::Road => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Air  => 'heroicon-o-paper-airplane',
            self::Sea  => 'heroicon-o-globe-alt',
            self::Road => 'heroicon-o-truck',
        };
    }
}
