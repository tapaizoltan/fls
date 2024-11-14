<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WhereDidAFindUs: string implements HasColor, HasIcon, HasLabel
{
    case GoogleAds = "1"; //GoogleAds
    case Facebook = "2"; //Facebook
    Case LinkedIn = "3"; //LinkedIn
    case Recommendation = "4"; //Ajánlás
    case Reseller = "5"; //Viszonteladó
    case ExhibitionOrEvent = "6"; //Kiállítás/Rendezvény
    case ColdCalling = "7"; //Hideghívás
    case Other = "8"; //Egyéb 

    public function getLabel(): string
    {
        return match ($this) {
            self::GoogleAds => 'GoogleAds',
            self::Facebook => 'Facebook',
            self::LinkedIn => 'LinkedIn',
            self::Recommendation => 'Ajánlás',
            self::Reseller => 'Viszonteladó',
            self::ExhibitionOrEvent => 'Kiállítás/Rendezvény',
            self::ColdCalling => 'Hideghívás',
            self::Other => 'Egyéb',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::GoogleAds => 'gray',
            self::Facebook => 'gray',
            self::LinkedIn => 'gray',
            self::Recommendation => 'gray',
            self::Reseller => 'gray',
            self::ExhibitionOrEvent => 'gray',
            self::ColdCalling => 'gray',
            self::Other => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::GoogleAds => 'tabler-brand-google',
            self::Facebook => 'tabler-brand-facebook',
            self::LinkedIn => 'tabler-brand-linkedin',
            self::Recommendation => 'tabler-mood-heart',
            self::Reseller => 'tabler-truck-return',
            self::ExhibitionOrEvent => 'tabler-stereo-glasses',
            self::ColdCalling => 'tabler-phone-calling',
            self::Other => 'tabler-eye-question',
        };
    }
}