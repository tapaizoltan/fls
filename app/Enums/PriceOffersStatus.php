<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PriceOffersStatus: int implements HasColor, HasIcon, HasLabel
{
    case UnderProcessing = 1; //feldolgozás alatt
    case Pending = 2; //függőben lévő
    case SuccessfulPriceOffer = 3; //sikeres ajánlat
    case UnsuccessfulPriceOffer = 4; //sikertelen ajánlat

    public function getLabel(): string
    {
        return match ($this) {
            self::UnderProcessing => 'Feldolgozás alatt',
            self::Pending => 'Függőben lévő',
            self::SuccessfulPriceOffer => 'Sikeres ajánlat',
            self::UnsuccessfulPriceOffer => 'Sikertelen ajánlat',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::UnderProcessing => 'warning',
            self::Pending => 'info',
            self::SuccessfulPriceOffer => 'success',
            self::UnsuccessfulPriceOffer => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::UnderProcessing => 'tabler-blender',
            self::Pending => 'tabler-ping-pong',
            self::SuccessfulPriceOffer => 'tabler-thumb-up',
            self::UnsuccessfulPriceOffer => 'tabler-thumb-down',
        };
    }
}