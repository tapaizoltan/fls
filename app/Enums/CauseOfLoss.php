<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CauseOfLoss: int implements HasColor, HasIcon, HasLabel
{
    case ItDidNotReachDecisionMaker = 1; //Döntéshozóig nem jutott el
    case Unreachable = 2; //Elérhetetlen
    Case MissingFeaturesOrServices = 3; //Hiányzó funkciók vagy szolgáltatások
    case ActualLater = 4; //Később aktuális
    case WeCouldNotFindOut = 5; //Nem tudtuk meg
    case YouDoNotNeedIt = 6; //Nincs rá szüksége
    case ItIsTooExpensive = 7; //Túl drága

    public function getLabel(): string
    {
        return match ($this) {
            self::ItDidNotReachDecisionMaker => 'Döntéshozóig nem jutott el',
            self::Unreachable => 'Elérhetetlen',
            self::MissingFeaturesOrServices => 'Hiányzó funkciók vagy szolgáltatások',
            self::ActualLater => 'Később aktuális',
            self::WeCouldNotFindOut => 'Nem tudtuk meg',
            self::YouDoNotNeedIt => 'Nincs rá szüksége',
            self::ItIsTooExpensive => 'Túl drága',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ItDidNotReachDecisionMaker => 'info',
            self::Unreachable => 'info',
            self::MissingFeaturesOrServices => 'info',
            self::ActualLater => 'info',
            self::WeCouldNotFindOut => 'info',
            self::YouDoNotNeedIt => 'info',
            self::ItIsTooExpensive => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ItDidNotReachDecisionMaker => 'tabler-hotel-service',
            self::Unreachable => 'tabler-hotel-service',
            self::MissingFeaturesOrServices => 'tabler-hotel-service',
            self::ActualLater => 'tabler-hotel-service',
            self::WeCouldNotFindOut => 'tabler-hotel-service',
            self::YouDoNotNeedIt => 'tabler-hotel-service',
            self::ItIsTooExpensive => 'tabler-hotel-service',
        };
    }
}