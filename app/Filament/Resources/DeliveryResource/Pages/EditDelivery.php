<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDelivery extends EditRecord
{
    protected static string $resource = DeliveryResource::class;

    /*
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = 6;
        return $data;
    }
    */

    protected function mutateFormDataBeforeSave(array $data): array
{
    // Frissítsük a Delivery státuszát
    $data['status'] = 6;

    // Ha van Priceoffer, frissítsük a Sale státuszát is
    if (isset($data['priceofferitems']) && count($data['priceofferitems']) > 0) {
        // Az első Priceofferitem alapján keresd meg a Sale rekordot
        $priceOfferItem = $data['priceofferitems'][0];  // Az első Priceofferitem, ha több is van
        $priceOfferId = $priceOfferItem['priceoffer_id'];  // Priceoffer ID
        $priceOffer = Priceoffer::find($priceOfferId); // Keresés a Priceoffer alapján
        
        // Ha létezik a kapcsolódó Priceoffer és annak Sale kapcsolata
        if ($priceOffer && $priceOffer->sale) {
            $sale = $priceOffer->sale;
            $sale->status = 7; // Állítsd a kívánt státuszra (pl. 7)
            $sale->save(); // Mentsd el a Sale rekordot
        }
    }

    return $data;
}
}
