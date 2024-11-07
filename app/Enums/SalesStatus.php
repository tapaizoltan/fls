<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SalesStatus: string implements HasColor, HasIcon, HasLabel
{
    case DemandAssessment = "1"; //igényfelmérés
    case Quotation = "2"; //árajánlat adás
    Case OfferFollowUp ="3"; //árajánlat utánkövetés
    case ConclusionOfContract = "4"; //szerződéskötés
    case SuccessfullyClosed = "5"; //sikeres lezárt
    case UnsuccessfullyClosed = "6"; //sikertelen lezárt

    public function getLabel(): string
    {
        return match ($this) {
            self::DemandAssessment => 'Igényfelmérés',
            self::Quotation => 'Árajánlat adás',
            self::OfferFollowUp => 'Árajánlat utánkövetés',
            self::ConclusionOfContract => 'Szerződéskötés',
            self::SuccessfullyClosed => 'Sikeresen lezárt',
            self::UnsuccessfullyClosed => 'Sikertelen lezárt',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::DemandAssessment => 'info',
            self::Quotation => 'warning',
            self::OfferFollowUp => 'warning',
            self::ConclusionOfContract => 'warning',
            self::SuccessfullyClosed => 'success',
            self::UnsuccessfullyClosed => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DemandAssessment => 'tabler-message-question',
            self::Quotation => 'tabler-keyboard',
            self::OfferFollowUp => 'tabler-s-turn-down',
            self::ConclusionOfContract => 'tabler-writing-sign',
            self::SuccessfullyClosed => 'tabler-thumb-up',
            self::UnsuccessfullyClosed => 'tabler-thumb-down',
        };
    }
}