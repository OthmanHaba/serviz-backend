<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FlagEnum: string implements HasColor, HasLabel
{
    case OPEN = 'open';
    case CLOSE = 'close';

    public function getColor(): string
    {
        return match ($this) {
            self::OPEN => 'success',
            self::CLOSE => 'danger',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::OPEN => 'Opened',
            self::CLOSE => 'Closed',
        };
    }
}
