<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SalesStatus: int implements HasColor, HasIcon, HasLabel
{
    case DemandAssessment = 1; //igényfelmérés
    case PriceOffer = 2; //árajánlat adás
    case ConclusionOfContract = 3; //szerződéskötés számlázás
    case SuccessfullyClosed = 4; //sikeres lezárt
    case UnsuccessfullyClosed = 5; //sikertelen lezárt
    case UnderDelivery = 6; //kiszállítás alatt
    case Delivered = 7; //átvéve

    public function getLabel(): string
    {
        return match ($this) {
            self::DemandAssessment => 'Igényfelmérés',
            self::PriceOffer => 'Árajánlat adás',
            self::ConclusionOfContract => 'Szerződéskötés számlázás',
            self::SuccessfullyClosed => 'Sikeresen lezárt',
            self::UnsuccessfullyClosed => 'Sikertelen lezárt',
            self::UnderDelivery => 'Kiszállítás alatt',
            self::Delivered => 'Kiszállítva',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::DemandAssessment => 'info',
            self::PriceOffer => 'warning',
            self::ConclusionOfContract => 'warning',
            self::SuccessfullyClosed => 'success',
            self::UnsuccessfullyClosed => 'danger',
            self::UnderDelivery => 'warning',
            self::Delivered => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DemandAssessment => 'tabler-message-question',
            self::PriceOffer => 'tabler-keyboard',
            self::ConclusionOfContract => 'tabler-writing-sign',
            self::SuccessfullyClosed => 'tabler-thumb-up',
            self::UnsuccessfullyClosed => 'tabler-thumb-down',
            self::UnderDelivery => 'tabler-truck-delivery',
            self::Delivered => 'tabler-truck-loading',
        };
    }
}