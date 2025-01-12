<?php

namespace App\Models;

use App\Enums\PriceOffersStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Priceoffer extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    protected $casts = [
        'status' => PriceOffersStatus::class,
    ];

    public static function generatePriceOfferId()
    {
        $lastPriceOfferId = Priceoffer::withTrashed()->latest('price_offer_id')->first();
        if ($lastPriceOfferId) {
            $lastNumber = (int) substr($lastPriceOfferId->price_offer_id, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        return 'PO-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceofferitems()
    {
        return $this->hasMany(Priceofferitem::class);
    }
}
