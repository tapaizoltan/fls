<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum EventTypes: string implements HasColor, HasIcon, HasLabel
{
    case Mapping = "1"; //feltérképezés
    case IssuingQuotation = "2"; //árajánlat kiadás
    case SaleInProgress = "3"; //értékesítés folyamatban
    case ClosedWon = "4"; //lezárt nyert
    case ClosedLost = "5"; //lezárt vesztett

    public function getLabel(): string
    {
        return match ($this) {
            self::Mapping => 'Feltérképezés',
            self::IssuingQuotation => 'Árajánlat kiadás',
            self::SaleInProgress => 'Értékesítés folyamatban',
            self::ClosedWon => 'Lezárt nyert',
            self::ClosedLost => 'Lezárt vesztett',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Mapping => 'info',
            self::IssuingQuotation => 'warning',
            self::SaleInProgress => 'warning',
            self::ClosedWon => 'success',
            self::ClosedLost => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Mapping => 'tabler-radar',
            self::IssuingQuotation => 'tabler-replace',
            self::SaleInProgress => 'tabler-cash',
            self::ClosedWon => 'tabler-thumb-up',
            self::ClosedLost => 'tabler-thumb-down',
        };
    }
}