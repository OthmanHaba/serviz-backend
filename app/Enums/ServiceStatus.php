<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceStatus implements HasColor, HasLabel
{
    case PendingUserApproved;
    case PendingProviderApproved;
    case InProgress;
    case Completed;

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PendingUserApproved => Color::Yellow,
            self::PendingProviderApproved => Color::Orange,
            self::InProgress => Color::Blue,
            self::Completed => Color::Green,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PendingUserApproved => 'Pending User Approval',
            self::PendingProviderApproved => 'Pending Provider Approval',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
        };
    }
}
